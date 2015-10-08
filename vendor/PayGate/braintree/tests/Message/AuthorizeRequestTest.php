<?php

namespace PayGate\Braintree\Message;

use PayGate\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /**
     * @var AuthorizeRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest(), \Braintree_Configuration::gateway());
        $this->request->initialize(
            array(
                'amount' => '10.00',
                'token' => 'abc123',
                'transactionId' => '684',
                'testMode' => false,
                'taxExempt' => false,
                'card' => [
                    'firstName' => 'Kayla',
                    'shippingCompany' => 'League',
                ]
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertArrayNotHasKey('paymentMethodToken', $data);
        $this->assertSame('abc123', $data['paymentMethodNonce']);
        $this->assertSame('10.00', $data['amount']);
        $this->assertSame('684', $data['orderId']);
        $this->assertSame('Kayla', $data['billing']['firstName']);
        $this->assertSame('League', $data['shipping']['company']);
        $this->assertFalse($data['options']['submitForSettlement']);
        $this->assertFalse($data['taxExempt']);

        // Check empty values are not sent
        $this->assertFalse(isset($data['billingAddressId']));

        $this->request->configure();
        $this->assertSame('production', \Braintree_Configuration::environment());
    }

    public function testPaymentMethodToken()
    {
        $this->request->initialize(
            array(
                'amount' => '10.00',
                'transactionId' => '684',
                'testMode' => false,
                'taxExempt' => false,
                'card' => [
                    'firstName' => 'Kayla',
                    'shippingCompany' => 'League',
                ],
                'paymentMethodToken' => 'fake-token-123'
            )
        );

        $data = $this->request->getData();
        $this->assertSame('fake-token-123', $data['paymentMethodToken']);
        $this->assertArrayNotHasKey('paymentMethodNonce', $data);
    }

    public function testPaymentMethodNonce()
    {
        $this->request->initialize(
            array(
                'amount' => '10.00',
                'transactionId' => '684',
                'testMode' => false,
                'taxExempt' => false,
                'card' => [
                    'firstName' => 'Kayla',
                    'shippingCompany' => 'League',
                ],
                'paymentMethodNonce' => 'abc123'
            )
        );

        $data = $this->request->getData();
        $this->assertSame('abc123', $data['paymentMethodNonce']);
        $this->assertArrayNotHasKey('paymentMethodToken', $data);
    }

    public function testSandboxEnvironment()
    {
        $this->request->setTestMode(true);

        $this->request->configure();
        $this->assertSame('sandbox', \Braintree_Configuration::environment());
    }
}
