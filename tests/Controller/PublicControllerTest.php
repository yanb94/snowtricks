<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    protected function setUp()
    {
    }

    /**
     * @dataProvider urlPublicOfController
     */
    public function testUrlFunctionnal($url, $response)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertSame($response, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlUserOfController
     */
    public function testUrlUserFunctionnal($url, $response)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW'   => 'pa$$word'
        ));

        $crawler = $client->request('GET', $url);

        $this->assertSame($response, $client->getResponse()->getStatusCode());
    }

    public function testError404()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vvdvdvdv');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function urlPublicOfController()
    {
        return [
            ['/', 200],
            ['/load-more-trick', 200]
        ];
    }

    public function urlUserOfController()
    {
        return [
            ['/add-trick',200],
        ];
    }
}
