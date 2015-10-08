<?php
/**
 * Payment Method
 */

namespace PayGate\Common;

/**
 * Payment Method
 *
 */
class PaymentMethod
{

    /**
     * The ID of the payment method.  
     *
     * @var string
     */
    protected $id;
    
    /**
     * The full name of the payment method
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new PaymentMethod
     *
     * @param string $id   The identifier of this payment method
     * @param string $name The name of this payment method
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * The identifier of this payment method
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The name of this payment method
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
