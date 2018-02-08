<?php

namespace App\Controller\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserControllerTest extends WebTestCase
{
    private $em;

    public function setUp()
    {
    }


    public function tearDown()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');
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


    public function testAuthentification()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();

        $form['_username'] = "perso";
        $form['_password'] = "password";

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('a.nav-link:contains("Déconnexion")')->count());
        $this->assertSame(1, $crawler->filter('a.nav-link:contains("Ajouter un trick")')->count());
    }

    public function testBadAuthentification()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();

        $form['_username'] = "perso";
        $form['_password'] = "falsepassword";

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-danger')->count());
    }

    /**
     * @dataProvider badFormRegisterData
     */
    public function testBadInscription($name, $email, $passwordFirst, $passwordSecond, $image)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Créer un compte')->form();

        $form['user[username]'] = $name;
        $form['user[email]'] = $email;
        $form['user[password][first]'] = $passwordFirst;
        $form['user[password][second]'] = $passwordSecond;
        $form['user[photo][file]'] = $image;

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testForgotPasswordRequest()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot_password');

        $form = $crawler->selectButton('Réinitialisez le mot de passe')->form();

        $form['forgot_password[username]'] = 'perso';

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(
            1,
            $crawler->filter('h1:contains("Demande de réinitialisation du mot de passe prise en compte")')->count()
        );
    }

    public function testForgotPasswordRequestBadUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot_password');

        $form = $crawler->selectButton('Réinitialisez le mot de passe')->form();

        $form['forgot_password[username]'] = 'noexist';

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testForgotPasswordRequestBlank()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot_password');

        $form = $crawler->selectButton('Réinitialisez le mot de passe')->form();

        $form['forgot_password[username]'] = '';

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }


    public function testConfirmInscription()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/confirm_user/my_test_validation_token');

        $this->assertSame(1, $crawler->filter('h1:contains("Inscription validé")')->count());

        $link = $crawler->selectLink('Se connecter')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Connexion')->form();

        $form['_username'] = "perso";
        $form['_password'] = "password";

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('a.nav-link:contains("Déconnexion")')->count());
        $this->assertSame(1, $crawler->filter('a.nav-link:contains("Ajouter un trick")')->count());
    }

    public function testConfirmInscriptionBad()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/confirm_user/my_false_test_validation_token');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider reinitBadData
     */
    public function testReinitPasswordBadData($email, $password_one, $password_two)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset_password/my_test_reset_token');

        $form = $crawler->selectButton('Réinitialisez le mot de passe')->form();

        $form['reset_password[email]'] = $email;
        $form['reset_password[password][first]'] = $password_one;
        $form['reset_password[password][second]'] = $password_two;

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testReinitPasswordBad()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset_password/my_false_test_reset_token');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testReinitPassword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset_password/my_test_reset_token');

        $form = $crawler->selectButton('Réinitialisez le mot de passe')->form();

        $form['reset_password[email]'] = "passwordemail@email.com";
        $form['reset_password[password][first]'] = "newpassword";
        $form['reset_password[password][second]'] = "newpassword";

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(
            1,
            $crawler->filter('div.alert.alert-success:contains("Le mot de passe à été réinitialisé")')->count()
        );
    }

    public function testInscription()
    {
        $image = new UploadedFile(
            __DIR__.'/../../public/test-file/test-img.jpg',
            'image.jpg',
            'image/jpeg',
            123,
            true
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Créer un compte')->form();

        $form['user[username]'] = "newPerso";
        $form['user[email]'] = "mysuperemail@email.com";
        $form['user[password][first]'] = "password";
        $form['user[password][second]'] = "password";
        $form['user[photo][file]'] = $image;

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h1:contains("Inscription prise en compte")')->count());
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

    public function badFormRegisterData()
    {
        $imageGood = new UploadedFile(
            __DIR__.'/../../public/test-file/test-img.jpg',
            'image.jpg',
            'image/jpeg',
            123,
            true
        );

        $imageBadType = new UploadedFile(
            __DIR__.'/../../public/test-file/text.txt',
            'text.txt',
            'text/plain',
            123,
            true
        );

        return[
            [
                "user" => "",
                "email" => "",
                "password_first" => "",
                "password_two" => "",
                "image" => ""
            ],
            [
                "user" => "newPerso",
                "email" => "yjesuisla",
                "password_first" =>"password",
                "password_two" =>"password",
                "image" => $imageBadType
            ],
            [
                "user" => "perso", // utilisateur qui existe déjà
                "email" => "mysuper@email.com",
                "password_first" => "password1",
                "password_two" => "password1",
                "image" => $imageGood
            ],
            [
                "user" => "newPerso",
                "email" => "mysuper@email.com",
                "password_first" => "password2",
                "password_two" => "pass",
                "image" => $imageGood
            ],
            [
                "user" => "newPerso",
                "email" => "mysuper@email.com",
                "password_first" => "password3",
                "password_two" => "password3",
                "image" => $imageBadType
            ],
        ];
    }

    public function reinitBadData()
    {
        return [
            [
                "email" => "",
                "password_first" => "",
                "password_second" => ""
            ],
            [
                "email" => "other@other.com",
                "password_first" => "newpassword",
                "password_second" => "newpassword"
            ],
            [
                "email" => "passwordemail@email.com",
                "password_first" => "newpassword",
                "password_second" => "newpa"
            ]

        ];
    }
}
