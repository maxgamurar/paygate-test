<?php

namespace PayGate\PayPal\Message;

use PayGate\Common\Message\AbstractResponse;
use PayGate\Common\Message\RequestInterface;

/**
 * PayPal Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        parse_str($data, $this->data);
    }

    public function isSuccessful()
    {
        return isset($this->data['ACK']) && in_array($this->data['ACK'], array('Success', 'SuccessWithWarning'));
    }

    public function getTransactionReference()
    {
        foreach (array('REFUNDTRANSACTIONID',
            'TRANSACTIONID',
            'PAYMENTINFO_0_TRANSACTIONID',
            'AUTHORIZATIONID') as $key) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
        }
    }

    public function getMessage()
    {
        return isset($this->data['L_LONGMESSAGE0']) ? $this->data['L_LONGMESSAGE0'] : null;
    }
}
