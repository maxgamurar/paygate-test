<?php

namespace PayGate\PayPal\Message;

use PayGate\Tests\TestCase;

class RestFetchTransactionRequestTest extends TestCase
{
    /** @var \PayGate\PayPal\Message\RestFetchTransactionRequest */
    private $request;

    public function setUp()
    {
        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();
        $this->request = new RestFetchTransactionRequest($client, $request);
    }

    public function testEndpoint()
    {
        $this->request->setTransactionReference('ABC-123');
        $this->assertStringEndsWith('/payments/sale/ABC-123', $this->request->getEndpoint());
    }
}
