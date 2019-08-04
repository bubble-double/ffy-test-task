<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Task description');
    }

    /**
     * @return void
     */
    public function testApiDocPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api-doc');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Api documentation');
    }
}
