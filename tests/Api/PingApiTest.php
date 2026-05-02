<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PingApiTest extends WebTestCase
{
    public function testPingEndpointReturnsExpectedJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/ping');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonStringEqualsJsonString(
            '{"message":"pong","status":"ok"}',
            (string) $client->getResponse()->getContent()
        );
    }
}
