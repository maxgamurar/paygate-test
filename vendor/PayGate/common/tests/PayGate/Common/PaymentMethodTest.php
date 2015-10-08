<?php

namespace PayGate\Common;

use PayGate\Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    public function testConstruct()
    {
        $method = new PaymentMethod('99', 'Acme Corp');

        $this->assertSame('99', $method->getId());
        $this->assertSame('Acme Corp', $method->getName());
    }
}
