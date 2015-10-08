<?php

namespace PayGate\Common;

use Mockery as m;
use PayGate\Tests\TestCase;

class GatewayFactoryTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        m::mock('alias:PayGate\\SpareChange\\TestGateway');
    }

    public function setUp()
    {
        $this->factory = new GatewayFactory;
    }

    public function testReplace()
    {
        $gateways = array('Foo');
        $this->factory->replace($gateways);

        $this->assertSame($gateways, $this->factory->all());
    }

    public function testRegister()
    {
        $this->factory->register('Bar');

        $this->assertSame(array('Bar'), $this->factory->all());
    }

    public function testRegisterExistingGateway()
    {
        $this->factory->register('Milky');
        $this->factory->register('Bar');
        $this->factory->register('Bar');

        $this->assertSame(array('Milky', 'Bar'), $this->factory->all());
    }

    public function testFindRegistersAvailableGateways()
    {
        $this->factory = m::mock('PayGate\Common\GatewayFactory[getSupportedGateways]');
        $this->factory->shouldReceive('getSupportedGateways')->once()
            ->andReturn(array('SpareChange_Test'));

        $gateways = $this->factory->find();

        $this->assertContains('SpareChange_Test', $gateways);
        $this->assertContains('SpareChange_Test', $this->factory->all());
    }

    public function testFindIgnoresUnavailableGateways()
    {
        $this->factory = m::mock('PayGate\Common\GatewayFactory[getSupportedGateways]');
        $this->factory->shouldReceive('getSupportedGateways')->once()
            ->andReturn(array('SpareChange_Gone'));

        $gateways = $this->factory->find();

        $this->assertEmpty($gateways);
        $this->assertEmpty($this->factory->all());
    }

    public function testCreateShortName()
    {
        $gateway = $this->factory->create('SpareChange_Test');
        $this->assertInstanceOf('\\PayGate\\SpareChange\\TestGateway', $gateway);
    }

    public function testCreateFullyQualified()
    {
        $gateway = $this->factory->create('\\PayGate\\SpareChange\\TestGateway');
        $this->assertInstanceOf('\\PayGate\\SpareChange\\TestGateway', $gateway);
    }

    /**
     * @expectedException \PayGate\Common\Exception\RuntimeException
     * @expectedExceptionMessage Class '\PayGate\Invalid\Gateway' not found
     */
    public function testCreateInvalid()
    {
        $gateway = $this->factory->create('Invalid');
    }

    public function testGetSupportedGateways()
    {
        $gateways = $this->factory->getSupportedGateways();

        $this->assertContains('Stripe', $gateways);
    }
}
