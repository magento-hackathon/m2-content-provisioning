<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Test\Integration\Model\Config;

use DOMDocument;
use Firegento\ContentProvisioning\Model\Config\Converter;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    protected $model;

    protected function setUp(): void
    {
        $this->model = Bootstrap::getObjectManager()
            ->create(Converter::class);
    }

    public function testConverter()
    {
        $pathFiles = __DIR__ . '/../../_files';
        $expectedResult = require $pathFiles . '/result.php';
        $path = $pathFiles . '/content_provisioning.xml';
        $domDocument = new DOMDocument();
        $domDocument->load($path);
        $result = $this->model->convert($domDocument);
        $this->assertEquals($expectedResult, $result);
    }
}
