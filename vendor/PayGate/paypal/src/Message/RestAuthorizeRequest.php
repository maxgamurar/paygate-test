<?php
/**
 * PayPal REST Authorize Request
 */

namespace PayGate\PayPal\Message;

/**
 * PayPal REST Authorize Request
 *
 */
class RestAuthorizeRequest extends AbstractRestRequest
{
    public function getData()
    {
        $data = array(
            'intent' => 'authorize',
            'payer' => array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array()
            ),
            'transactions' => array(
                array(
                    'description' => $this->getDescription(),
                    'amount' => array(
                        'total' => $this->getAmount(),
                        'currency' => $this->getCurrency(),
                    ),
                )
            )
        );

        $items = $this->getItems();
        if ($items) {
            $itemList = array();
            foreach ($items as $n => $item) {
                $itemList[] = array(
                    'name' => $item->getName(),
                    'description' => $item->getDescription(),
                    'quantity' => $item->getQuantity(),
                    'price' => $this->formatCurrency($item->getPrice()),
                    'currency' => $this->getCurrency()
                );
            }
            $data['transactions'][0]['item_list']["items"] = $itemList;
        }

        if ($this->getCardReference()) {
            $this->validate('amount');

            $data['payer']['funding_instruments'][] = array(
                'credit_card_token' => array(
                    'credit_card_id' => $this->getCardReference(),
                ),
            );
        } elseif ($this->getCard()) {
            $this->validate('amount', 'card');
            $this->getCard()->validate();

            $data['payer']['funding_instruments'][] = array(
                'credit_card' => array(
                    'number' => $this->getCard()->getNumber(),
                    'type' => $this->getCard()->getBrand(),
                    'expire_month' => $this->getCard()->getExpiryMonth(),
                    'expire_year' => $this->getCard()->getExpiryYear(),
                    'cvv2' => $this->getCard()->getCvv(),
                    'first_name' => $this->getCard()->getFirstName(),
                    'last_name' => $this->getCard()->getLastName(),
                    'billing_address' => array(
                        'line1' => $this->getCard()->getAddress1(),
                        //'line2' => $this->getCard()->getAddress2(),
                        'city' => $this->getCard()->getCity(),
                        'state' => $this->getCard()->getState(),
                        'postal_code' => $this->getCard()->getPostcode(),
                        'country_code' => strtoupper($this->getCard()->getCountry()),
                    )
                )
            );

            $line2 = $this->getCard()->getAddress2();
            if (!empty($line2)) {
                $data['payer']['funding_instruments'][0]['credit_card']['billing_address']['line2'] = $line2;
            }
        } else {
            $this->validate('amount', 'returnUrl', 'cancelUrl');

            unset($data['payer']['funding_instruments']);

            $data['payer']['payment_method'] = 'paypal';
            $data['redirect_urls'] = array(
                'return_url' => $this->getReturnUrl(),
                'cancel_url' => $this->getCancelUrl(),
            );
        }

        return $data;
    }

    /**
     * Get transaction description.
     *
     * @return string
     */
    public function getDescription()
    {
        $id = $this->getTransactionId();
        $desc = parent::getDescription();

        if (empty($id)) {
            return $desc;
        } elseif (empty($desc)) {
            return $id;
        } else {
            return "$id : $desc";
        }
    }

    /**
     * Get transaction endpoint.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . '/payments/payment';
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new RestAuthorizeResponse($this, $data, $statusCode);
    }
}
