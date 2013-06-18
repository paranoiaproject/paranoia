<?php
namespace Payment;

use \Payment\Exception\UnknownPaymentAdapter;

class Factory
{
    /**
    * @var \Zend_Config
    */
    private $_config;
    
    /**
    * returns a adapter instance that defined adaptername in 
    * configuration.
    * @param Zend_Config $config
    * @return \Payment\Adapter\AdapterInterface
    */
    private function _getAdapter($config)
    {
        $adapter = $config->adapter;
        $adapterClass = '\\Payment\\Adapter\\{$adapter}';
        if( ! class_exists($adapterClass) ) {
            throw new UnknownPaymentAdapter('Unknown payment adapter : ' . 
                                            $adapterClass);
        }
        
        return new $adapterClass($config);
    }
    
    /**
    * factory constructor.
    * @param Zend_Config $config
    */
    public function __construct(\Zend_Config $config)
    {
        $this->_config = $config;
    }
    
    /**
    * creates a new adapter instance by the specified pos key.
    * @param string $poskey
    * @return \Payment\Adapter\AdapterInterface
    */
    public function createInstance($posKey)
    {
        if( ! array_key_exists($config, $posKey) ) {
            throw new UnknownPos('Unknown pos : ' . $posKey);
        }
        $config = $this->_config[$posKey];
        return $this->_getAdapter($config);
    }
}
