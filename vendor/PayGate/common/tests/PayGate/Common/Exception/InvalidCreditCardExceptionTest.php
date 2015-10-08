<?php

namespace PayGate\Common\Exception;

use PayGate\Tests\TestCase;

class InvalidCreditCardExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new InvalidCreditCardException('Oops');
        $this->assertSame('Oops', $exception->getMessage());
    }
}
