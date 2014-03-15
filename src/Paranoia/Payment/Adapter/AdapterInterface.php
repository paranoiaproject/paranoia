<?php
namespace Paranoia\Payment\Adapter;

use Paranoia\Payment\Request;

interface AdapterInterface
{

    /**
     * @param Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function preAuthorization( Request $request );

    /**
     * @param Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function postAuthorization( Request $request );

    /**
     * @param Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function sale( Request $request );

    /**
     * @param Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function cancel( Request $request );

    /**
     * @param Request $request
     *
     * @return \Paranoia\Payment\Response\PaymentResponse
     */
    public function refund( Request $request );
}
