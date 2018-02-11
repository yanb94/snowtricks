<?php

namespace App\Controller\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlPublicOfController
     */
    public function testUrlFunctionnal($url, $response)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertSame($response, $client->getResponse()->getStatusCode());
    }

    public function urlPublicOfController()
    {
        return [
            ['/login', 200],
            ['/register', 200],
            ['/register_confirm', 200],
            ['/forgot_password', 200],
            ['/forgot_password_confirm', 200],
            ['/reset_password_confirm', 200],
            ['/logout', 302],
        ];
    }
}
