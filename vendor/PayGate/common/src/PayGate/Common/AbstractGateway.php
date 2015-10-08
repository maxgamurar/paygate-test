<?php
/**
 * Base payment gateway class
 */

namespace PayGate\Common;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Client as HttpClient;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Base payment gateway class
 *
 */
abstract class AbstractGateway implements GatewayInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * @var \Guzzle\Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $httpRequest;

    /**
     * Create a new payment gateway instance
     *
     * @param ClientInterface $httpClient  Guzzle client for API calls
     * @param HttpRequest     $httpRequest Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $this->httpClient = $httpClient ?: $this->getDefaultHttpClient();
        $this->httpRequest = $httpRequest ?: $this->getDefaultHttpRequest();
        $this->initialize();
    }

    public function getShortName()
    {
        return Helper::getGatewayShortName(get_class($this));
    }

    public function initialize(array $parameters = array())
    {
        $this->parameters = new ParameterBag;

        foreach ($this->getDefaultParameters() as $key => $value) {
            if (is_array($value)) {
                $this->parameters->set($key, reset($value));
            } else {
                $this->parameters->set($key, $value);
            }
        }

        Helper::initialize($this, $parameters);

        return $this;
    }

    public function getDefaultParameters()
    {
        return array();
    }

    public function getParameters()
    {
        return $this->parameters->all();
    }

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }

    public function getCurrency()
    {
        return strtoupper($this->getParameter('currency'));
    }

    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Supports Authorize
     *
     * @return boolean True if this gateway supports the authorize() method
     */
    public function supportsAuthorize()
    {
        return method_exists($this, 'authorize');
    }

    /**
     * Supports Complete Authorize
     *
     * @return boolean True if this gateway supports the completeAuthorize() method
     */
    public function supportsCompleteAuthorize()
    {
        return method_exists($this, 'completeAuthorize');
    }

    /**
     * Supports Capture
     *
     * @return boolean True if this gateway supports the capture() method
     */
    public function supportsCapture()
    {
        return method_exists($this, 'capture');
    }

    /**
     * Supports Purchase
     *
     * @return boolean True if this gateway supports the purchase() method
     */
    public function supportsPurchase()
    {
        return method_exists($this, 'purchase');
    }

    /**
     * Supports Complete Purchase
     *
     * @return boolean True if this gateway supports the completePurchase() method
     */
    public function supportsCompletePurchase()
    {
        return method_exists($this, 'completePurchase');
    }

    /**
     * Supports Refund
     *
     * @return boolean True if this gateway supports the refund() method
     */
    public function supportsRefund()
    {
        return method_exists($this, 'refund');
    }

    /**
     * Supports Void
     *
     * @return boolean True if this gateway supports the void() method
     */
    public function supportsVoid()
    {
        return method_exists($this, 'void');
    }

    /**
     * Supports CreateCard
     *
     * @return boolean True if this gateway supports the create() method
     */
    public function supportsCreateCard()
    {
        return method_exists($this, 'createCard');
    }

    /**
     * Supports DeleteCard
     *
     * @return boolean True if this gateway supports the delete() method
     */
    public function supportsDeleteCard()
    {
        return method_exists($this, 'deleteCard');
    }

    /**
     * Supports UpdateCard
     *
     * @return boolean True if this gateway supports the update() method
     */
    public function supportsUpdateCard()
    {
        return method_exists($this, 'updateCard');
    }

    /**
     * Create and initialize a request object
     *
     * @param string $class The request class name
     * @param array $parameters
     * @return \PayGate\Common\Message\AbstractRequest
     */
    protected function createRequest($class, array $parameters)
    {
        $obj = new $class($this->httpClient, $this->httpRequest);

        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }

    /**
     * Get the global default HTTP client.
     *
     * @return HttpClient
     */
    protected function getDefaultHttpClient()
    {
        return new HttpClient(
            '',
            array(
                'curl.options' => array(CURLOPT_CONNECTTIMEOUT => 90),
            )
        );
    }

    /**
     * Get the global default HTTP request.
     *
     * @return HttpRequest
     */
    protected function getDefaultHttpRequest()
    {
        return HttpRequest::createFromGlobals();
    }
}
