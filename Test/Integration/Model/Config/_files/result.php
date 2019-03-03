<?php

use \Firegento\ContentProvisioning\Api\Data\PageEntryInterface;
use \Firegento\ContentProvisioning\Api\Data\BlockEntryInterface;

return [
    'pages' => [
        'test.page.1' => [
            PageEntryInterface::TITLE => 'Test Page 1',
            PageEntryInterface::CONTENT => file_get_contents(__DIR__ . '/test-files/file-1.html'),
            PageEntryInterface::KEY => 'test.page.1',
            PageEntryInterface::IDENTIFIER => 'test-page-1',
            PageEntryInterface::IS_ACTIVE => true,
            PageEntryInterface::IS_MAINTAINED => true,
            PageEntryInterface::STORES => ['admin'],
            PageEntryInterface::CONTENT_HEADING => '',
        ],
        'test.page.2' => [
            PageEntryInterface::TITLE => 'Title 2',
            PageEntryInterface::CONTENT => file_get_contents(__DIR__ . '/test-files/file-1.html'),
            PageEntryInterface::KEY => 'test.page.2',
            PageEntryInterface::IDENTIFIER => 'test-page-2',
            PageEntryInterface::IS_ACTIVE => false,
            PageEntryInterface::IS_MAINTAINED => false,
            PageEntryInterface::STORES => ['default', 'admin'],
            PageEntryInterface::CONTENT_HEADING => 'New Page Heading 2',
            PageEntryInterface::META_TITLE => 'SEO Page Title',
            PageEntryInterface::META_KEYWORDS => 'Some, SEO, keywords',
            PageEntryInterface::META_DESCRIPTION => 'SEO description',
            PageEntryInterface::PAGE_LAYOUT => '3columns',
            PageEntryInterface::LAYOUT_UPDATE_XML => '<foo>bar</foo>',
            PageEntryInterface::CUSTOM_THEME_FROM => '2019-03-03',
            PageEntryInterface::CUSTOM_THEME_TO => '2019-03-29',
            PageEntryInterface::CUSTOM_THEME => '3',
            PageEntryInterface::CUSTOM_ROOT_TEMPLATE => '3columns',
        ]
    ],
    'blocks' => [
        'test.block.1' => [
            BlockEntryInterface::TITLE => 'Test Block 1',
            BlockEntryInterface::CONTENT => '<h2>test foobar Aenean commodo ligula eget dolor aenean massa</h2>',
            BlockEntryInterface::KEY => 'test.block.1',
            BlockEntryInterface::IDENTIFIER => 'test-block-1',
            BlockEntryInterface::IS_ACTIVE => true,
            BlockEntryInterface::IS_MAINTAINED => true,
            BlockEntryInterface::STORES => ['admin'],
        ],
        'test.block.2' => [
            BlockEntryInterface::TITLE => 'Test Block 2',
            BlockEntryInterface::CONTENT => file_get_contents(__DIR__ . '/test-files/file-1.html'),
            BlockEntryInterface::KEY => 'test.block.2',
            BlockEntryInterface::IDENTIFIER => 'test-block-2',
            BlockEntryInterface::IS_ACTIVE => true,
            BlockEntryInterface::IS_MAINTAINED => false,
            BlockEntryInterface::STORES => ['default', 'admin'],
        ],
    ],
];
