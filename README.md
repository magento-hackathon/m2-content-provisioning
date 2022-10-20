# FireGento Magento 2 Content Provisioning

This module was developed during a Magento Hackathon organized by FireGento e.V. (https://firegento.com/).

> **ℹ️ Maintenance**
> 
> This module is maintained by [TechDivision](https://www.techdivision.com/). Therefore, there we created a mirror repository, which allow us 
> to run automated quality checks on our internal infrastructure for this module. Please see https://gitlab.met.tdintern.de/techdivision-public/m2-content-provisioning
> for details. Please feel free to contact us, if you have questions regarding the repository structure or mirroring.

#### Build-Status (`develop` branch)
[![pipeline status](https://gitlab.met.tdintern.de/techdivision-public/m2-content-provisioning/badges/develop/pipeline.svg)](https://gitlab.met.tdintern.de/techdivision-public/m2-content-provisioning/-/commits/develop)

## The idea behind this module

It is a common requirement, that some parts of content (like CMS pages or blocks) need be deployed within a release.
There are content entries, which should be maintained by code all the time and some content just needs delivered one
time to each system.

In most cases such requirements will be solved by setup scripts (or setup patches), which is possible way there is no
chance to declare the responsibility for each content entity.

This module allows you to declare such content entries via XML file and ensures, that this declaration will be applied
to database on each `setup:upgrade` run.

## Install with composer

```bash
composer require firegento/magento2-content-provisioning
```

## How it works

After installing this module you can create your own `content_provisioning.xml` in each of your modules.

## Example configurations

### Minimal configuration for a page

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento_ContentProvisioning:etc/content_provisioning.xsd">
    <page key="your-module.page.an-identifier.all" identifier="an-identifier" maintained="true" active="true">
        <title>Page Title</title>
        <content type="file">Your_Module::path/to/content.html</content>
    </page>
    ...
</config>
```

### Full configuration for a page

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento_ContentProvisioning:etc/content_provisioning.xsd">
    <page key="your-module.page.an-identifier.german" identifier="an-identifier" maintained="true" active="true">
        <title>Page Title</title>
        <content heading="New Page Heading" type="file">Your_Module::path/to/content.html</content>
        <media_directory>Your_Module::path/to/media</media_directory>
        <stores>
            <store code="germany_german"/>
            <store code="swiss_german"/>
            <store code="austria_german"/>
        </stores>
        <seo>
            <title>SEO Page Title</title>
            <keywords>Some, SEO, keywords</keywords>
            <description>SEO description</description>
        </seo>
        <design>
            <layout>3columns</layout>
            <layout_xml><![CDATA[<foo>bar</foo>]]></layout_xml>
        </design>
        <custom_design>
            <from>2019-03-03</from>
            <to>2019-03-29</to>
            <layout>3columns</layout>
            <theme_id>3</theme_id>
        </custom_design>
    </page>
    ...
</config>
```

### Minimal configuration for a block

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento_ContentProvisioning:etc/content_provisioning.xsd">
    <block key="your-module.block.lorem-ipsum-1.all" identifier="lorem-ipsum-1" maintained="true" active="true">
        <title>Test Block 1</title>
        <content><![CDATA[<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>]]></content>
    </block>
    ...
</config>
```

### Full configuration for a block

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento_ContentProvisioning:etc/content_provisioning.xsd">
    <block key="your-module.block.lorem-ipsum-2.german" identifier="lorem-ipsum-2" maintained="false" active="true">
        <title>Test Block 2</title>
        <content type="file">Your_Module::path/to/content.html</content>
        <media_directory>Your_Module::path/to/media</media_directory>
        <stores>
            <store code="germany_german"/>
            <store code="swiss_german"/>
            <store code="austria_german"/>
        </stores>
    </block>
    ...
</config>
```

## Some explanation

### `key`-Attribute

The `key` attribute is required in order to merge all content provisioning configurations across all modules. It is like
the `name` attribute for layout blocks...

#### You could use `identifier` - or?

No, identifier is not unique since the same identifier can be used for multiple store views.

### `maintained`-Attribute

With this attribute you define whether this content should be applied every time or even only once. Is the value
`false` the content will only be persisted, if there is no `identifier` for the defined stores present in database.

### `content`-Node

This node provide THE content for your page or block. It can be added as node value in a CDATA block or as a file path,
which is relative to your Magento instance or prefixed by a module namespace. In order to use files you need to add
the `type="file"` attribute to the content node.

### `stores`-Node

This node is optional. If it is not defined, the block or page will be applied to all stores. A "maintained" entry will
also be applied to stores, which will be created in the future after re-running `setup:upgrade` command. You can also
use the 'wildcard' `*` in order to define that the content should be applied to all stores.

### `media_directory`-Node (since version 1.2.0)

Specifies the directory for media files. Each used media file in the content and present in media source directory will
be copied to Magento's `pub/media` directory. Sub-directory structure should be same like inspected it to be
in `pub/media`. Only existing and used media files will be copied.

## Executing integration tests on local environment

```bash
# Create a new Magento instance
composer create-project --repository=https://repo.magento.com/ magento/project-community-edition magento

# Install content provisioning extension
cd magento 
composer require firegento/magento2-content-provisioning

# Update database configuration for integration tests
mv dev/tests/integration/etc/install-config-mysql.php.dist dev/tests/integration/etc/install-config-mysql.php
vi dev/tests/integration/etc/install-config-mysql.php

# Execute tests
php vendor/bin/phpunit -c $(pwd)/vendor/firegento/magento2-content-provisioning/Test/Integration/phpunit.xml
```

## Console Commands
```shell
# reset a CMS block (all localizations) by its key
bin/magento content-provisioning:block:reset --key "myKey"
bin/magento content-provisioning:block:reset -k "myKey"

# reset a CMS blocks by its identifier
bin/magento content-provisioning:block:reset --identifier "myIdentifier"
bin/magento content-provisioning:block:reset -i "myIdentifier"

# add a CMS block by key
bin/magento content-provisioning:block:apply "myKey"

# add a CMS page by key
bin/magento content-provisioning:page:apply "myKey"

# list all configured CMS block entries
bin/magento content-provisioning:block:list

# list all configured CMS page entries
bin/magento content-provisioning:page:list
```

## Issues and planned features

See issues to see what's planed next: https://github.com/magento-hackathon/m2-content-provisioning/issues
Feel free to add your ideas there.

## Changelog

See [changelog file](CHANGELOG.md)

## Extensions for this extension ;)

For [`magenerds/pagedesigner`](https://github.com/Magenerds/PageDesigner) there is an module, which extends this content
provisioning
module: [`techdivision/pagedesigner-content-provisioning`](https://github.com/techdivision/pagedesigner-content-provisioning)
.

