# magento2-content-provisioning
This module was developed during a Magento Hackathon organized by FireGento e.V. (https://firegento.com/).

## The idea behind this module
It is a common requirement, that some parts of content (like CMS pages or blocks) need be deployed within
a release. There are content entries, which should be maintained by code all the time and some content just needs
delivered one time to each system.

In most cases such requirements will be solved by setup scripts (or setup patches), which is possible way there is
no chance to declare the responsibility for each content entity.

This module allows you to declare such content entries via XML file and ensures, that this declaration will be applied
to database on each `setup:upgrade` run.

## Install with composer
```bash
composer config repositories.firegento.content-provisioning vcs https://github.com/magento-hackathon/m2-content-provisioning.git
composer require firegento/magento2-content-provisioning
```

## How it works
After installing this module you can create own `content_provisioning.xml` in each of your modules. 

## Example configurations

### Minimal configuration for a page
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd">
    <page identifier="an-identifier" maintained="true" active="true">
        <title>Page Title</title>
        <content type="file">Your_Module::path/to/content.html</content>
    </page>
    ...
</config>
```

### Full configuration for a page
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd">
    <page identifier="an-identifier" maintained="true" active="true">
        <title>Page Title</title>
        <content heading="New Page Heading" type="file">Your_Module::path/to/content.html</content>
        <stores>
            <store code="germany_german" />
            <store code="swiss_german" />
            <store code="austria_german" />
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
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd">
    <block identifier="lorem-ipsum-1" maintained="true" active="true">
        <title>Test Block 1</title>
        <content><![CDATA[<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>]]></content>
    </block>
    ...
</config>
```

### Full configuration for a block
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Firegento/ContentProvisioning/etc/content_provisioning.xsd">
    <block identifier="lorem-ipsum-2" maintained="false" active="true">
        <title>Test Block 2</title>
        <content type="file">Your_Module::path/to/content.html</content>
        <stores>
            <store code="germany_german" />
            <store code="swiss_german" />
            <store code="austria_german" />
        </stores>
    </block>
    ...
</config>
```

## Some explanation

### `maintain`-Attribute
With this attribute you define whether this content should be applied every time or even only once. Is the value
`false` the content will only be persisted, if there is no `identifier` for the defined stores present in database.

### `content`-Node
This node provide THE content for your page or block. It can be added as node value in a CDATA block or as a
file path, which is relative to your Magento instance or prefixed by a module namespace. In order to use files
you need to add the `type="file"` attribute to the content node.

### `stores`-Node
This node is optional. If it is not defined, the block or page will be applied to all stores. A "maintained" entry
will also be applied to stores, which will be created in the future after re-running `setup:upgrade` command.

## Planed features / Road map
| Status | Feature | Issue |
|---|---|---|
| implemented | Configuration for pages | |
| implemented | Configuration for blocks | |
| implemented | Recurring setup installer for pages | |
| implemented | Recurring setup installer for blocks | |
| planned | Notification in Magento backend (admin), for editors - if the content entry is maintained by code | #1 |
| planned | CLI command to apply dedicated configured content entries to database | |
| idea | Add a button to page or block edit page in Magento backend, if there is content defined for this page in code. (Like: "Use default content") | |
| idea | Persist version hash every time the content is applied to database and track whether is was changes by editor. (Auto `maintained` mode) | |

