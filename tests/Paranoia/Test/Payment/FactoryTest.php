<?php
namespace Paranoia\Tests\Payment;

use \PHPUnit_Framework_TestCase;
use \Exception;
use Paranoia\Payment\Factory;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    private $config;

    public function setUp()
    {
        parent::setUp();
        $configFile = dirname(__FILE__) . '/../../../Resources/config/config.json';
        if(!file_exists($configFile)) {
            throw new Exception('Configuration file does not exist.');
        }
        $config = file_get_contents($configFile);
        $this->config = json_decode($config);
    }

    public function provider()
    {
        return array(
            array('estbank', '\\Paranoia\\Payment\\Adapter\\Est'),
            array('garantibank', '\\Paranoia\\Payment\\Adapter\\Gvp'),
        );
    }

    /**
     * @param $configKey
     * @param string $className
     * @dataProvider provider
     */
    public function testInstanceCreation($configKey, $className)
    {
        $instance = Factory::createInstance($this->config, $configKey);
        $this->assertInstanceOf($className, $instance);
    }
}
