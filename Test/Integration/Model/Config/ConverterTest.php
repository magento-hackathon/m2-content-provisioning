<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Config;

use Magento\Framework\Exception\ConfigurationMismatchException;
use Firegento\ContentProvisioning\Model\Config\Converter;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Converter
     */
    protected $model;

    protected function setUp(): void
    {
        $this->model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(Converter::class);
    }

    public function testConverter()
    {
        $pathFiles = __DIR__ . '/../../_files';
        $expectedResult = require $pathFiles . '/result.php';
        $path = $pathFiles . '/content_provisioning.xml';
        $domDocument = new \DOMDocument();
        $domDocument->load($path);
        $result = $this->model->convert($domDocument);
        $this->assertEquals($expectedResult, $result);
    }
}
