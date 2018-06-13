<?php
namespace Paranoia;

class TransactionType
{
    const SALE = 'sale';
    const REFUND = 'refund';
    const CANCEL = 'cancel';
    const PRE_AUTHORIZATION = 'pre_authorization';
    const POST_AUTHORIZATION = 'post_authorization';
    const POINT_INQUIRY = 'point_inquiry';
    const POINT_USAGE = 'point_usage';
}
