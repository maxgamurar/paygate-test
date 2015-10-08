<?php
/**
 * PayPal Pro Class using REST API
 */

namespace PayGate\PayPal;

use PayGate\Common\AbstractGateway;
use PayGate\PayPal\Message\ProAuthorizeRequest;
use PayGate\PayPal\Message\CaptureRequest;
use PayGate\PayPal\Message\RefundRequest;

/**
 * PayPal using REST API
 *
  */
class RestGateway extends AbstractGateway
{
    public function getName()
    {
        return 'PayPal REST';
    }

    public function getDefaultParameters()
    {
        return array(
            'clientId'     => '',
            'secret'       => '',
            'token'        => '',
            'testMode'     => false,
        );
    }

    /**
     * Get OAuth 2.0 client ID for the access token.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    /**
     * Set OAuth 2.0 client ID for the access token.
     * 
     *
     * @param string $value
     * @return RestGateway provides a fluent interface
     */
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * Get OAuth 2.0 secret for the access token.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * Set OAuth 2.0 secret for the access token.
     *
     * @param string $value
     * @return RestGateway
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * Get OAuth 2.0 access token.
     *
     * @param bool $createIfNeeded [optional] 
     * @return string
     */
    public function getToken($createIfNeeded = true)
    {
        if ($createIfNeeded && !$this->hasToken()) {
            $response = $this->createToken()->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                if (isset($data['access_token'])) {
                    $this->setToken($data['access_token']);
                    $this->setTokenExpires(time() + $data['expires_in']);
                }
            }
        }

        return $this->getParameter('token');
    }

    /**
     * Create OAuth 2.0 access token request.
     *
     * @return \PayGate\PayPal\Message\RestTokenRequest
     */
    public function createToken()
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestTokenRequest', array());
    }

    /**
     * Set OAuth 2.0 access token.
     * 
     * @param string $value
     * @return RestGateway provides a fluent interface
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Get OAuth 2.0 access token expiry time.
     * 
     * @return integer
     */
    public function getTokenExpires()
    {
        return $this->getParameter('tokenExpires');
    }

    /**
     * Set OAuth 2.0 access token expiry time.
     * 
     * @param integer $value
     * @return RestGateway provides a fluent interface
     */
    public function setTokenExpires($value)
    {
        return $this->setParameter('tokenExpires', $value);
    }

    /**
     * Is there a bearer token and is it still valid?
     *
     * @return bool
     */
    public function hasToken()
    {
        $token = $this->getParameter('token');

        $expires = $this->getTokenExpires();
        if (!empty($expires) && !is_numeric($expires)) {
            $expires = strtotime($expires);
        }

        return !empty($token) && time() < $expires;
    }

    /**
     * Create Request
     *
     * @param string $class
     * @param array $parameters
     * @return \PayGate\PayPal\Message\AbstractRestRequest
     */
    public function createRequest($class, array $parameters = array())
    {
        if (!$this->hasToken() && $class != '\PayGate\PayPal\Message\RestTokenRequest') {
            $this->getToken(true);
        }

        return parent::createRequest($class, $parameters);
    }

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestPurchaseRequest', $parameters);
    }

    /**
     * Completes a purchase request.
     *
     * @param array $parameters
     * @return Message\AbstractRestRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestCompletePurchaseRequest', $parameters);
    }


    /**
     * Create an authorization request.
     * 
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestAuthorizeRequest', $parameters);
    }

    /**
     * Capture an authorization.
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestCaptureRequest', $parameters);
    }


    /**
     * Fetch a Sale Transaction
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestFetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestFetchTransactionRequest', $parameters);
    }

    /**
     * Refund a Sale Transaction
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestRefundRequest', $parameters);
    }

    /**
     * Store a credit card in the vault
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestCreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestCreateCardRequest', $parameters);
    }

    /**
     * Delete a credit card from the vault.
     *
     * @param array $parameters
     * @return \PayGate\PayPal\Message\RestDeleteCardRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\PayGate\PayPal\Message\RestDeleteCardRequest', $parameters);
    }
}
