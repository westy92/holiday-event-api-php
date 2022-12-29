<?php

namespace Westy92\HolidayEventApi\Tests;

use Westy92\HolidayEventApi\Client;
use PHPUnit\Framework\TestCase;

final class ConstructorTest extends TestCase
{
    public function testBlankApiKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please provide a valid API key. Get one at https://apilayer.com/marketplace/checkiday-api#pricing.');
        new Client('');
    }

    public function testNullApiKey(): void
    {
        $this->expectException(\TypeError::class);
        new Client(null);
    }

    public function testMissingApiKey(): void
    {
        $this->expectException(\ArgumentCountError::class);
        new Client();
    }

    public function testConstructorSuccess(): void
    {
        $client = new Client('abc123');
        $this->assertNotNull($client);
    }
}
