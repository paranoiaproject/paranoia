<?php
namespace Payment\Adapter;

use \Payment\Request;

interface AdapterInterface
{
    public function sale(Request $request);
    public function cancel(Request $request);
    public function refund(Request $request);
}
