<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Bootstrap;

return [
    'db-host' => '127.0.0.1',
    'db-user' => 'root',
    'db-password' => '',
    'db-name' => 'magento_integration_tests',
    'db-prefix' => 'trv_',
    'backend-frontname' => 'backend',
    'admin-user' => Bootstrap::ADMIN_NAME,
    'admin-password' => Bootstrap::ADMIN_PASSWORD,
    'admin-email' => Bootstrap::ADMIN_EMAIL,
    'admin-firstname' => Bootstrap::ADMIN_FIRSTNAME,
    'admin-lastname' => Bootstrap::ADMIN_LASTNAME,
    'amqp-host' => 'localhost',
    'amqp-port' => '5672',
    'amqp-user' => 'guest',
    'amqp-password' => 'guest',
    'elasticsearch-host' => '127.0.0.1',
    'elasticsearch-port' => '9200',
];
