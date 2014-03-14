<?php
namespace Paranoia\Payment;

interface TransferInterface
{
    /**
     * returns transaction raw data.
     * @return string
     */
    public function getRawData();

    /**
     * sets raw transaction data to object.
     * @param string $rawData
     * @return self
     */
    public function setRawData($rawData);

    /**
     * returns transaction time.
     * @return integer
     */
    public function getTime();

    /**
     * sets transaction time to object.
     * @param $time
     * @return self
     */
    public function setTime($time);

    /**
     * returns transaction type.
     * @return string
     */
    public function getTransactionType();

    /**
     * sets transaction type to response object.
     * @param string $transactionType
     * @return self
     */
    public function setTransactionType($transactionType);

}

