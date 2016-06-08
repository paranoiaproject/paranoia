<?php
namespace Paranoia\Validator;

use Paranoia\Transfer\TransferInterface;

interface ValidatorInterface
{
    public function validate(TransferInterface $object);
}