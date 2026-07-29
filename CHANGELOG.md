# CHANGELOG

## 1.7.0

* *Compatibility:* Add Magento 2.4.9 / PHP 8.4 compatibility
  * Remove PHP 8.1 / 8.2 / 8.3 support, require PHP ^8.4
  * Bump `magento/framework` requirement to `~103.0.9`
  * Fix fatal error in `content-provisioning:block:apply`, `content-provisioning:page:apply` and
    `content-provisioning:page:list` CLI commands: `execute()` did not declare the `: int` return type
    required by `symfony/console` 7.x (bundled with Magento 2.4.9), which caused a fatal
    `Declaration ... must be compatible with Command::execute(): int` error on class load
  * Add `: void` return type to all `configure()` overrides in `Model/Console/*Command.php` for consistency
  * Fix `Symfony\Component\Console\Helper\Table::render()` calls in `BlockListCommand` and `PageListCommand`:
    the method no longer accepts an `OutputInterface` argument in `symfony/console` 7.x
  * Fix incorrect `@var` docblocks in `AddBlockCommand`/`AddPageCommand` (properties were documented as the
    plain query class instead of their `*Factory` counterpart, which is what is actually injected/stored)
  * Remove `xdebug.max_nesting_level` ini setting from `Test/Integration/phpunit.gitlab.xml` and
    `phpunit9.gitlab.xml`
  * Add PHPStan (level 5, phpVersion 80400) static analysis configuration with a baseline for pre-existing
    findings
  * Add `AGENTS.md`

## 1.6.0

* *Improvement:* Refactoring code base of module

## 1.5.0

* *Feature:* Detect inconsistencies in the CMS blocks

## 1.4.3

* *Improvement:* Use di proxies for console commands

## 1.4.2

* *Improvement:* Soften dependencies

## 1.4.1

* *Bugfix:* Remove quotes form regexp to restore media provisioning feature

## 1.4.0

* *Feature:* Introduce reset CLI command to force content import for already given contents manually

## 1.3.7

* PHP 8.1 support

## 1.3.6

* Change to MIT licence
* No other changes between 1.3.5 to 1.3.6

## 1.3.5

* *Improvement:* Add support for full-width cms pages

## 1.3.4

* *Bugfix:* Set correct XSD URN
* *Bugfix:* Pin composer to Version 1

## 1.3.3

* *Bugfix:* Fix return type of convert on error

## 1.3.2

* *Bugfix:* Fix xml schema reference to pass magento static tests

## 1.3.0 / 1.3.1

* *Feature:* Add Magento 2.4 support
* *Feature:* Add phpUnit 9 support

## 1.2.4

* *Feature:* Enable PHP 7.2 support
* *Feature:* Enable PHP 7.3 support
* *Feature:* Add command to add blocks and pages

## 1.2.2 / 1.2.3

* *Bugfix:* `composer.json` blocked installation of the module in Magento 2.2
  ** Change `composer.json` dependency definition
  ** Remove some type and return type hints in order to support Magento's code generation in older versions

## 1.2.1

* *Improvement:* Better extensibility for fetching media files from content (PR from @roma-glushko)

## 1.2.0

* *Feature:* Enables module to provide media files
  ** Introduce optional XML node for pages and blocks: `media_directory`
  ** Media files, which are used in the content will be copied to Magento's `pub/media` directory if they are present in
  the defined source media directory

## 1.1.1

* *Bugfix:* In some cases `bin/magento` execution was not able at all during the initial Magento install

## 1.1.0

* *Feature:* Introduce further interfaces and refactor config reader structure, in order to improve extensibility

## 1.0.4

* *Bugfix:* Backport bugfix from version 1.1.1

## 1.0.3

* *Bugfix:* Fix issue with missing `store_id` values while persisting block entries.

## 1.0.2

* *Bugfix:* Extend schema in order to allow `.` for `key` like it is shown in the examples

## 1.0.1

* *Bugfix:* Add PHP 7.1 compatibility

## 1.0.0

* *Feature:* Notification in Magento backend (admin), for editors - if the content entry is maintained by code
* *Improvement:* Introduce `key` attribute for configured entries, in order to improve merging of all configurations
* *Refactoring:* Improve query for fetching existing cms entities by configured entries

## 0.1.0

* *Feature:* Implement initial functionality
  ** Configuration for pages
  ** Configuration for blocks
  ** Recurring setup installer for pages
  ** Recurring setup installer for blocks
