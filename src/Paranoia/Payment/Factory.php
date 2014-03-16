<?php
namespace Paranoia\Payment;

use \StdClass;
use Paranoia\Payment\Exception\UnknownAdapter;
use Paranoia\Payment\Exception\UnknownPos;
use Paranoia\Payment\Adapter\AdapterInterface;

class Factory
{

    /**
     * returns a adapter instance that defined adaptername in
     * configuration.

     */
    private static function getAdapter($config)
    {
        $adapter      = $config->adapter;
        $adapterClass = "\\Paranoia\\Payment\\Adapter\\{$adapter}";
        if (! class_exists($adapterClass)) {
            throw new UnknownAdapter(
                'Unknown payment adapter : ' . $adapterClass
            );
        }
        return new $adapterClass( $config );
    }

    /**
     * creates a new adapter instance by the specified pos key.
     *
     * @param StdClass $config
     * @param string   $paymentMethod
     *
     * @throws Exception\UnknownPos
     * @return AdapterInterface
     */
    public static function createInstance(StdClass$config, $paymentMethod)
    {
        if (! isset($config->{$paymentMethod})) {
            throw new UnknownPos('Unknown pos : ' . $paymentMethod);
        }
        return self::getAdapter($config->{$paymentMethod});
    }
}
