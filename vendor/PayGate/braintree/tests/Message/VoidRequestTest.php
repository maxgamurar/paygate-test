<?php

namespace PayGate\Braintree\Message;

use PayGate\Tests\TestCase;

class VoidRequestTest extends TestCase
{
    /**
     * @var VoidRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest(), \Braintree_Configuration::gateway());
        $this->request->initialize(
            array(
                'transactionReference' => 'abc123',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('abc123', $data['transactionReference']);
    }

}
