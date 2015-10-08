<?php
namespace PayGate\Braintree\Message;

use PayGate\Common\Exception\InvalidRequestException;
use PayGate\Common\Message\ResponseInterface;

/**
 * Authorize Request
 *
 * @method Response send()
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');

        $data = [
            'amount' => $this->getAmount(),
            'billingAddressId' => $this->getBillingAddressId(),
            'channel' => $this->getChannel(),
            'customFields' => $this->getCustomFields(),
            'customerId' => $this->getCustomerId(),
            'descriptor' => $this->getDescriptor(),
            'deviceData' => $this->getDeviceData(),
            'deviceSessionId' => $this->getDeviceSessionId(),
            'merchantAccountId' => $this->getMerchantAccountId(),
            'orderId' => $this->getTransactionId(),
            'purchaseOrderNumber' => $this->getPurchaseOrderNumber(),
            'recurring' => $this->getRecurring(),
            'shippingAddressId' => $this->getShippingAddressId(),
            'taxAmount' => $this->getTaxAmount(),
            'taxExempt' => $this->getTaxExempt(),
        ];

        // special validation
        if ($this->getPaymentMethodToken()) {
            $data['paymentMethodToken'] = $this->getPaymentMethodToken();
        } elseif($this->getToken()) {
            $data['paymentMethodNonce'] = $this->getToken();
        } else {
            throw new InvalidRequestException("The token (payment nonce) or paymentMethodToken field should be set.");
        }

        // Remove null values
        $data = array_filter($data, function($value){
            return ! is_null($value);
        });

        $data += $this->getOptionData();
        $data += $this->getCardData();
        $data['options']['submitForSettlement'] = false;

        return $data;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $response = $this->braintree->transaction()->sale($data);

        return $this->createResponse($response);
    }
}
