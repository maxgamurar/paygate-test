<?php

namespace PayGate\Braintree\Message;

use PayGate\Common\Message\RequestInterface;

/**
 * Response
 */
class CustomerResponse extends Response
{
    public function getCustomerData()
    {
        return $this->data->customer;
    }
}
