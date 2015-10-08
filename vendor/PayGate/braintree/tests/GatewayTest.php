<?php

namespace PayGate\Braintree;

use PayGate\Tests\GatewayTestCase;
use PayGate\Common\CreditCard;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => '10.00',
            'token' => 'abcdef',
        );
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));
        $this->assertInstanceOf('PayGate\Braintree\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(array('amount' => '10.00'));
        $this->assertInstanceOf('PayGate\Braintree\Message\CaptureRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCreateCustomer()
    {
        $request = $this->gateway->createCustomer();
        $this->assertInstanceOf('PayGate\Braintree\Message\CreateCustomerRequest', $request);
    }

    public function testDeleteCustomer()
    {
        $request = $this->gateway->deleteCustomer();
        $this->assertInstanceOf('PayGate\Braintree\Message\DeleteCustomerRequest', $request);
    }

    public function testUpdateCustomer()
    {
        $request = $this->gateway->updateCustomer();
        $this->assertInstanceOf('PayGate\Braintree\Message\UpdateCustomerRequest', $request);
    }

    public function testCreateMerchantAccount()
    {
        $request = $this->gateway->createMerchantAccount();
        $this->assertInstanceOf('PayGate\Braintree\Message\CreateMerchantAccountRequest', $request);
    }
    
    public function testUpdateMerchantAccount()
    {
        $request = $this->gateway->updateMerchantAccount();
        $this->assertInstanceOf('PayGate\Braintree\Message\UpdateMerchantAccountRequest', $request);
    }

    public function testCreatePaymentMethod()
    {
        $request = $this->gateway->createPaymentMethod();
        $this->assertInstanceOf('PayGate\Braintree\Message\CreatePaymentMethodRequest', $request);
    }

    public function testDeletePaymentMethod()
    {
        $request = $this->gateway->deletePaymentMethod();
        $this->assertInstanceOf('PayGate\Braintree\Message\DeletePaymentMethodRequest', $request);
    }

    public function testUpdatePaymentMethod()
    {
        $request = $this->gateway->updatePaymentMethod();
        $this->assertInstanceOf('PayGate\Braintree\Message\UpdatePaymentMethodRequest', $request);
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));
        $this->assertInstanceOf('PayGate\Braintree\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(array('amount' => '10.00'));
        $this->assertInstanceOf('PayGate\Braintree\Message\RefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testVoid()
    {
        $request = $this->gateway->void();
        $this->assertInstanceOf('PayGate\Braintree\Message\VoidRequest', $request);
    }

    public function testFind()
    {
        $request = $this->gateway->find(array());
        $this->assertInstanceOf('PayGate\Braintree\Message\FindRequest', $request);
    }

    public function testClientToken()
    {
        $request = $this->gateway->clientToken(array());
        $this->assertInstanceOf('PayGate\Braintree\Message\ClientTokenRequest', $request);
    }
}
