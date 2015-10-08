<?php
/**
 * PayGate Gateway Factory class
 */

namespace PayGate\Common;

use Guzzle\Http\ClientInterface;
use PayGate\Common\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * PayGate Gateway Factory class
 *
 */
class GatewayFactory
{
    /**
     * Internal storage for all gateways
     *
     * @var array
     */
    private $gateways = array();

    /**
     * All available gateways
     *
     * @return array An array of gateway names
     */
    public function all()
    {
        return $this->gateways;
    }

    /**
     * Replace the list of available gateways
     *
     * @param array $gateways An array of gateway names
     */
    public function replace(array $gateways)
    {
        $this->gateways = $gateways;
    }

    /**
     * Register a new gateway
     *
     * @param string $className Gateway name
     */
    public function register($className)
    {
        if (!in_array($className, $this->gateways)) {
            $this->gateways[] = $className;
        }
    }

    /**
     * Automatically find and register all supported gateways
     *
     * @return array An array of gateway names
     */
    public function find()
    {
        foreach ($this->getSupportedGateways() as $gateway) {
            $class = Helper::getGatewayClassName($gateway);
            if (class_exists($class)) {
                $this->register($gateway);
            }
        }

        ksort($this->gateways);

        return $this->all();
    }

    /**
     * Create a new gateway instance
     *
     * @param string               $class       Gateway name
     * @param ClientInterface|null $httpClient  Guzzle HTTP Client implementation
     * @param HttpRequest|null     $httpRequest Symfony HTTP Request implementation
     * @throws RuntimeException                 If no gateway found
     * @return object                           An object of class $class is created and returned
     */
    public function create($class, ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $class = Helper::getGatewayClassName($class);

        if (!class_exists($class)) {
            throw new RuntimeException("Class '$class' not found");
        }

        return new $class($httpClient, $httpRequest);
    }

    /**
     * Get a list of supported gateways which may be available
     *
     * @return array
     */
    public function getSupportedGateways()
    {
        $package = json_decode(file_get_contents(__DIR__.'/../../../composer.json'), true);

        return $package['extra']['gateways'];
    }
}
