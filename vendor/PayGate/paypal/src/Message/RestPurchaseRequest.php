<?php
/**
 * PayPal REST Purchase Request
 */

namespace PayGate\PayPal\Message;

/**
 * PayPal REST Purchase Request
 *
 */
class RestPurchaseRequest extends RestAuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();
        $data['intent'] = 'sale';
        return $data;
    }
}
