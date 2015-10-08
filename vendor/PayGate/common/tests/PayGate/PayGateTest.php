<?php

namespace PayGate;

use Mockery as m;
use PayGate\Tests\TestCase;

class PayGateTest extends TestCase
{
    public function tearDown()
    {
        PayGate::setFactory(null);
    }

    public function testGetFactory()
    {
        PayGate::setFactory(null);

        $factory = PayGate::getFactory();
        $this->assertInstanceOf('PayGate\Common\GatewayFactory', $factory);
    }

    public function testSetFactory()
    {
        $factory = m::mock('PayGate\Common\GatewayFactory');

        PayGate::setFactory($factory);

        $this->assertSame($factory, PayGate::getFactory());
    }

    public function testCallStatic()
    {
        $factory = m::mock('PayGate\Common\GatewayFactory');
        $factory->shouldReceive('testMethod')->with('some-argument')->once()->andReturn('some-result');

        PayGate::setFactory($factory);

        $result = PayGate::testMethod('some-argument');
        $this->assertSame('some-result', $result);
    }
}
