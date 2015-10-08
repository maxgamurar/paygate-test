<?php
namespace PayGate\Braintree\Message;

use PayGate\Common\Message\ResponseInterface;

/**
 * Authorize Request
 *
 * @method UpdateCustomerRequest send()
 */
class UpdateCustomerRequest extends AbstractRequest
{
    public function getData()
    {
        return array(
            'customerData' => $this->getCustomerData(),
            'customerId' => $this->getCustomerId(),
        );
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $response = $this->braintree->customer()->update($data['customerId'], $data['customerData']);

        return $this->response = new CustomerResponse($this, $response);
    }
}
