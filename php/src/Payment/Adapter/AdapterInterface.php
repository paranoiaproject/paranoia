<?php
namespace Payment\Adapter;

use \Payment\Request;

interface AdapterInterface
{
    public function preAuthorization(Request $request);
    public function postAuthorization(Request $request);
    public function sale(Request $request);
    public function cancel(Request $request);
    public function refund(Request $request);
}
