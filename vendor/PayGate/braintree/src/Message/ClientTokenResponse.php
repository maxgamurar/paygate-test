<?php

namespace PayGate\Braintree\Message;

use PayGate\Common\Message\AbstractResponse;
use PayGate\Common\Message\RequestInterface;

/**
 * Response
 */
class ClientTokenResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return true;
    }

    public function getToken()
    {
        return $this->data;
    }
}
