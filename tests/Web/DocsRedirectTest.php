<?php

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocsRedirectTest extends WebTestCase
{
    public function testDocsRedirectsToApiDocs(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs');

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/api/docs');
    }
}
