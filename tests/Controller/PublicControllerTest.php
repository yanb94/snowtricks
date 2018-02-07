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
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
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
        echo shell_exec('php bin/console doctrine:schema:drop --env=test --force');
        echo shell_exec('php bin/console doctrine:schema:create --env=test');
        echo shell_exec('php bin/console doctrine:fixtures:load --env=test');

        return [
            ['/', 200],
            ['/load-more-trick', 200],
            ['/add-trick',200],
            ['/edit-trick/1',200],
            ['/remove-trick/1',302],
            ['/remove-picture/5',302],
            ['/remove-video/3',302],
        ];
    }
}
