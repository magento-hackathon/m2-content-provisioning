# AGENTS.md — FireGento Content Provisioning

AI agent guidance for working on the `firegento/magento2-content-provisioning` Magento 2 module
(`Firegento_ContentProvisioning`).

## Module Overview

**Name:** `Firegento_ContentProvisioning`
**Composer package:** `firegento/magento2-content-provisioning`
**Type:** Full Magento 2 module (recurring setup installer, admin UI, CLI commands, cron job)
**Purpose:** Lets other modules declare CMS pages/blocks via a `content_provisioning.xml` file. Entries are
(re-)applied to the database on every `setup:upgrade` run. Entries can be `maintained` (always overwritten by
code) or one-shot (only created once, then left alone for editors).

Originally created during a FireGento e.V. hackathon, now actively maintained by TechDivision via a mirror
repository (see README.md for details). Do not assume this is a passive read-only mirror — the `develop`
branch here receives real feature/fix work.

## Compatibility

| Requirement | Version |
|-------------|---------|
| Magento | >= 2.4.9 |
| PHP | ^8.4 |
| `magento/framework` | ~103.0.9 |
| `magento/module-cms` / `-widget` / `-backend` / `-ui` / `-store` / `-config` | loose `>=` bounds (kept intentionally wide for backward compatibility with older Magento installs — do not tighten without a good reason, see CHANGELOG "Soften dependencies" history) |

There is **no Hyvä-compatibility module** maintained for this repository at this time.

## Directory Structure

```
m2-content-provisioning/
├── Api/                          # Public interfaces (@api)
│   ├── Data/                     # EntryInterface, PageEntryInterface, BlockEntryInterface
│   ├── ConfigParserInterface.php
│   ├── ConfigurationInterface.php
│   ├── ContentResolverInterface.php
│   ├── MediaFilesParserInterface.php
│   ├── StoreCodeResolverInterface.php
│   └── TargetMediaDirectoryPathProviderInterface.php
├── Block/                        # Admin block "content is maintained by code" warning + Save button blocks
├── Controller/Adminhtml/         # Admin Save controllers for pages/blocks (edit form save)
├── Cron/
│   └── InconsistenciesInCmsBlock.php   # Detects DB content that has drifted from XML config, emails a report
├── Exception/
│   └── CommandInputException.php
├── Model/
│   ├── BlockEntry.php / PageEntry.php        # Data models (extend Magento\Cms\Model\Block/Page)
│   ├── BlockInstaller.php / PageInstaller.php # Applies configured entries during setup:upgrade
│   ├── Command/                  # ApplyBlockEntry, ApplyPageEntry, ApplyMediaFiles, NormalizeData
│   ├── Config/                   # XML config reading: Converter, Data, NodeConverter, SchemaLocator, Parser/*
│   ├── Console/                  # CLI commands (see below)
│   ├── DTO/CmsBlockDto.php
│   ├── Query/                    # Small single-purpose query/lookup classes (Get*, Is*, Has*)
│   ├── Resolver/                 # Content/store-code resolvers
│   ├── ResourceModel/            # Raw DB lookups (GetCmsBlockByIdentifier, GetDbCmsBlockContent)
│   └── Validator/                # CanApplyBlockEntry, CanApplyPageEntry
├── Service/
│   ├── BlockQueryService.php     # Orchestrates the inconsistency-detection query flow used by the cron job
│   └── EmailService.php          # Sends the inconsistency report email
├── Setup/
│   └── RecurringData.php         # InstallDataInterface — runs BlockInstaller/PageInstaller on every setup:upgrade
├── ViewModel/
│   ├── BlockDataProvider.php         # Builds a Symfony Table from all configured block entries
│   └── ChangedBlockDataProvider.php  # Builds a Symfony Table from changed/inconsistent block entries
├── etc/
│   ├── module.xml, di.xml, config.xml, crontab.xml
│   └── content_provisioning.xsd  # XSD schema for the `content_provisioning.xml` files consumed from other modules
├── Test/Integration/             # PHPUnit integration tests (no Unit tests exist)
├── phpstan.neon / phpstan-baseline-5.neon
├── registration.php, composer.json, README.md, CHANGELOG.md
```

## Key Classes

### `Model/Console/*Command.php` — CLI commands
All extend `Symfony\Component\Console\Command\Command`.

| Command | Class | Notes |
|---|---|---|
| `content-provisioning:block:apply` | `AddBlockCommand` | Applies a single block entry by key |
| `content-provisioning:page:apply` | `AddPageCommand` | Applies a single page entry by key |
| `content-provisioning:block:list` | `BlockListCommand` | Lists all/changed block entries as a table |
| `content-provisioning:page:list` | `PageListCommand` | Lists all page entries as a table |
| `content-provisioning:block:reset` | `BlockResetCommand` | Force-reapplies block(s) by `--key`/`--identifier`, bypassing `maintained` semantics |

**Critical constraint for this module:** Magento 2.4.9 bundles `symfony/console` 7.x, whose `Command::execute()`
is declared as `abstract protected function execute(InputInterface $input, OutputInterface $output): int`.
**Every** `execute()` override in this module (and any new command added) **must** declare `: int` and actually
`return` an int (`Command::SUCCESS` / `Command::FAILURE` / `Magento\Framework\Console\Cli::RETURN_SUCCESS` /
`RETURN_FAILURE`). Omitting or mistyping this return type is a **fatal `Declaration ... must be compatible`
error at class-load time**, not a mere deprecation — this exact bug existed in `AddBlockCommand`,
`AddPageCommand`, and `PageListCommand` before the 2.4.9 compatibility update and was fixed there.

Similarly, `Symfony\Component\Console\Helper\Table::render()` takes **zero** arguments in symfony/console 7.x
(the `OutputInterface` is only accepted in the constructor `new Table($output)`). Calling `->render($output)`
does not crash (PHP silently ignores extra positional arguments to userland methods) but is stale/incorrect —
keep it argument-less if you touch this code.

### `Model/BlockInstaller` / `Model/PageInstaller`
Invoked from `Setup/RecurringData::install()` on every `setup:upgrade`. Iterate all configured entries
(merged from every module's `content_provisioning.xml` via `Model/Config/*`), and for each entry:
- If `maintained="true"`: always (re-)write content to the DB, matched per configured store
- If `maintained="false"`: only create if no matching entity already exists for that store

### `Model/Config/*` — XML configuration pipeline
`Converter` (registered via `di.xml`) transforms the merged `content_provisioning.xml` DOM into arrays keyed by
`key` attribute, using two virtual-typed `ParserChain`s (one for `page`, one for `block`) built from the
`Parser/*` classes (`MetaDataParser`, `StoresParser`, `ContentParser`, `SeoParser`, `DesignParser`,
`CustomDesignParser`, `MediaDirectoryParser`, etc.). `SchemaLocator` points at
`etc/content_provisioning.xsd` for merge-time validation.

### `Cron/InconsistenciesInCmsBlock`
Scheduled via `etc/crontab.xml` (config path `trans_email/ident_content_provisioning/cron_schedule`, default
`0 7 * * *`). Compares configured block content against what's actually in the DB
(`Service/BlockQueryService`), and if drift is found, emails an HTML report
(`Service/EmailService`) — throttled by `trans_email/ident_content_provisioning/email_frequency`
(`Model/Config/Source/EmailFrequency`) using a cache-stored last-sent timestamp.

## Coding Conventions

- `declare(strict_types=1)` in every PHP file
- Native param/return types are used throughout newer code (`Model/Console/BlockListCommand.php`,
  `Model/Console/BlockResetCommand.php`, `Cron/*`, `Service/*`) — older classes (`Model/BlockEntry.php`,
  `Model/PageEntry.php`) intentionally keep doc-block-only typed getters/setters to mirror the
  `Magento\Cms\Model\Block`/`Page` parent convention (magic `getData()`/`setData()`); don't add native types
  there without checking PHPStan/parent-class compatibility first
- No constructor property promotion is used in most existing classes — follow the surrounding file's style
  when editing; prefer explicit typed property declarations for new code
- Keep `@var`/`@param`/`@return` docblocks **accurate** — PHPStan trusts docblock types over the actual
  assigned class in some cases (see the `AddBlockCommand`/`AddPageCommand` factory-vs-non-factory docblock
  bug fixed in the 2.4.9 compatibility update as a cautionary example)

## Static Analysis

Run PHPStan from the module root (deployed under `vendor/firegento/magento2-content-provisioning/` in a real
Magento install, so Magento/Symfony classes are resolvable):

```bash
../../bin/phpstan analyse . -c phpstan.neon
```

- Level: 5
- phpVersion: 80400
- `Test/` is excluded from analysis
- `phpstan-baseline-5.neon` captures 16 pre-existing (not 2.4.9/PHP 8.4-related) type-strictness findings —
  mostly `Magento\Cms\Model\Block` vs `BlockInterface`/`PageInterface` mismatches and a few docblock/return
  type inaccuracies in `Controller/Adminhtml/*/Save.php`, `Model/Command/Apply*.php`, `Model/Query/*.php`,
  `Model/ResourceModel/*.php`. These are intentionally not fixed as part of the compatibility update to avoid
  untested behavioral changes; feel free to fix the underlying issues and shrink the baseline in a follow-up,
  but re-verify test coverage first
- Standard Magento-specific ignore rules are pre-configured in `phpstan.neon` (magic getters/setters, Factory
  classes, etc.)

## Tests

- **No Unit tests exist.** Only `Test/Integration/` exists, covering `BlockInstaller`, `PageInstaller`
  (including media file installation), the config `Converter`, and `FetchMediaFilesChain`.
- Two PHPUnit config variants are kept for CI: `Test/Integration/phpunit.gitlab.xml` (PHPUnit 9.1 schema,
  `<extensions>`) and `phpunit9.gitlab.xml` (PHPUnit 9.5 schema, `<listeners>`) — the CI test-executor tooling
  selects the appropriate one for the Magento/PHPUnit version under test.
- `mikey179/vfsstream` (require-dev) is used by
  `Test/Integration/Model/PageInstaller/InstallMediaFilesTest.php` to mock the media filesystem — keep this
  dependency.
- Run locally against a real Magento instance:
  ```bash
  php vendor/bin/phpunit -c $(pwd)/vendor/firegento/magento2-content-provisioning/Test/Integration/phpunit.xml
  ```

## Common Tasks for AI Agents

**Adding a new CLI command:** Create it under `Model/Console/`, extend `Symfony\Component\Console\Command\Command`,
register it in `etc/di.xml`'s console command list. Always declare `execute(InputInterface $input, OutputInterface $output): int`
and `configure(): void`, and return a proper `Command::SUCCESS`/`FAILURE` (or `Magento\Framework\Console\Cli::RETURN_*`) constant.

**Adding a new `content_provisioning.xml` node/attribute:** Extend `etc/content_provisioning.xsd`, add a
matching `Parser/*` class implementing `Api\ConfigParserInterface`, and wire it into the relevant `ParserChain`
virtual type (`PageParserChain`/`BlockParserChain`) in `etc/di.xml`.

**Changing inconsistency-detection/email logic:** Edit `Cron/InconsistenciesInCmsBlock.php` and
`Service/BlockQueryService.php` / `Service/EmailService.php`. Config paths live under
`trans_email/ident_content_provisioning/*` (see `etc/config.xml`).

**Updating dependencies:** Edit `composer.json` and update `CHANGELOG.md`/`README.md` accordingly. Keep
`magento/module-*` constraints loose (`>=`) unless there's a concrete compatibility reason to tighten them —
this package is also consumed outside of TechDivision-internal Magento installs.
