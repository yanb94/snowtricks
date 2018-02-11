<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    public function testIndexWork()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testError404()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vvdvdvdv');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
}
