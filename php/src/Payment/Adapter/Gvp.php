<?php
namespace Payment\Adapter;

use \Array2XML;

use \Payment\Request;
use Payment\Response\PaymentResponse;
use \Payment\Adapter\AdapterInterface;
use \Payment\Adapter\Container\Http;
use \Payment\Exception\UnexpectedResponse;

class Est extends Http implements AdapterInterface
{
    private function buildBaseRequest()
    {

    }

}
