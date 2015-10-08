<?php

namespace PayGate\Braintree;

use PayGate\Common\AbstractGateway;
use Braintree_Gateway;
use Braintree_Configuration;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
/**
 * Braintree Gateway
 */
class Gateway extends AbstractGateway
{
    /**
     * @var \Braintree_Gateway
     */
    protected $braintree;

    /**
     * Create a new gateway instance
     *
     * @param ClientInterface $httpClient  Guzzle client for API calls
     * @param HttpRequest     $httpRequest Symfony HTTP request object
     * @param Braintree_Gateway $braintree The Braintree gateway
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null, Braintree_Gateway $braintree = null)
    {
        $this->braintree = $braintree ?: Braintree_Configuration::gateway();

        parent::__construct($httpClient, $httpRequest);
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequest($class, array $parameters)
    {
        $obj = new $class($this->httpClient, $this->httpRequest, $this->braintree);

        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }

    public function getName()
    {
        return 'Braintree';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'publicKey' => '',
            'privateKey' => '',
            'testMode' => false,
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function setPublicKey($value)
    {
        return $this->setParameter('publicKey', $value);
    }

    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    /**
     * @param array $parameters
     * @return Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\AuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\CaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\ClientTokenRequest
     */
    public function clientToken(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\ClientTokenRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\CreateCustomerRequest
     */
    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\CreateCustomerRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\DeleteCustomerRequest
     */
    public function deleteCustomer(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\DeleteCustomerRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\UpdateCustomerRequest
     */
    public function updateCustomer(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\UpdateCustomerRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function find(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\FindRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\CreateMerchantAccountRequest
     */
    public function createMerchantAccount(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\CreateMerchantAccountRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\UpdateMerchantAccountRequest
     */
    public function updateMerchantAccount(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\UpdateMerchantAccountRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\CreatePaymentMethodRequest
     */
    public function createPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\CreatePaymentMethodRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\DeletePaymentMethodRequest
     */
    public function deletePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\DeletePaymentMethodRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\UpdatePaymentMethodRequest
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\UpdatePaymentMethodRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\RefundRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\PurchaseRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\PayGate\Braintree\Message\VoidRequest', $parameters);
    }
}
