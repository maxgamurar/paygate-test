<?php

namespace PayGate\Common\Exception;

use PayGate\Tests\TestCase;

class InvalidRequestExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new InvalidRequestException('Oops');
        $this->assertSame('Oops', $exception->getMessage());
    }
}
