<?php
/**
 * Payment gateway interface
 */

namespace PayGate\Common;

/**
 * Payment gateway interface
 *
 */
interface GatewayInterface
{
    /**
     * Get gateway display name
     *
     */
    public function getName();

    /**
     * Get gateway short name
     *
     */
    public function getShortName();

    /**
     * Define gateway parameters
     *
     */
    public function getDefaultParameters();

    /**
     * Initialize gateway with parameters
     */
    public function initialize(array $parameters = array());

    /**
     * Get all gateway parameters
     *
     * @return array
     */
    public function getParameters();
}
