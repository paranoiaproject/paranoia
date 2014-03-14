<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Payment\Request;

interface AdapterInterface
{
    public function preAuthorization(Request $request);
    public function postAuthorization(Request $request);
    public function sale(Request $request);
    public function cancel(Request $request);
    public function refund(Request $request);
}
