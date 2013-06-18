<?php
namespace Payment;

use \Payment\Exception\UnknownAdapter;
use \Payment\Exception\UnknownPos;

class Factory
{
    /**
    * returns a adapter instance that defined adaptername in 
    * configuration.
    *
    * @param Zend_Config $config
    * @return \Payment\Adapter\AdapterInterface
    */
    private static function _getAdapter($config)
    {
        $adapter = $config->adapter;
        $adapterClass = "\\Payment\\Adapter\\{$adapter}";
        if( ! class_exists($adapterClass) ) {
            throw new UnknownAdapter('Unknown payment adapter : ' . 
                                            $adapterClass);
        }
        return new $adapterClass($config);
    }
    
    /**
    * creates a new adapter instance by the specified pos key.
    *
    * @param \Zend_Config $config
    * @param string $paymentMethod
    * @return \Payment\Adapter\AdapterInterface
    */
    public static function createInstance(\Zend_Config $config, $paymentMethod)
    {
        if( ! isset($config->{$paymentMethod}) ) {
            throw new UnknownPos('Unknown pos : ' . $paymentMethod);
        }
        return self::_getAdapter($config->{$paymentMethod});
    }
}
