<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackofficeAccessTest extends WebTestCase
{
    public function testAnonymousUserIsRedirectedToLoginOnBackoffice(): void
    {
        $client = static::createClient();
        $client->request('GET', '/backoffice');

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/login');
    }
}
