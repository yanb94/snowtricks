<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function testAddTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/add-trick');

        $form = $crawler->selectButton('Ajouter un trick')->form();

        $form['figure[name]'] = "Nouvelle Figure";
        $form['figure[description]'] = "Je suis une nouvelle figure";
        $form['figure[group]'] = 1;

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }

    public function testEditTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/2');

        $form = $crawler->selectButton('Editer le trick')->form();

        $form['edit_figure[name]'] = "Nouvelle Figure Editer";
        $form['edit_figure[description]'] = "Je suis une nouvelle figure";
        $form['edit_figure[group]'] = 1;

        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h1:contains("Nouvelle Figure Editer")')->count());
    }

    /**
     * @dataProvider badFormData
     */
    public function testEditBadFormTrick($name, $description)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/2');

        $form = $crawler->selectButton('Editer le trick')->form();

        $form['edit_figure[name]'] = $name;
        $form['edit_figure[description]'] = $description;
        $form['edit_figure[group]'] = 1;

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    /**
     * @dataProvider badFormData
     */
    public function testAddBadFormTrick($name, $description)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/add-trick');

        $form = $crawler->selectButton('Ajouter un trick')->form();

        $form['figure[name]'] = $name;
        $form['figure[description]'] = $description;
        $form['figure[group]'] = 1;

        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testAddComment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/trick/sad');

        $form = $crawler->selectButton('Laisse un Commentaire')->form();

        $monCommentaire = "Ceci est un commentaire unique ".base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $form['comment[contenu]'] = $monCommentaire;
 
        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.my-comment-contenu:contains("'.$monCommentaire.'")')->count());
    }

    public function testAddCommentBad()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/trick/sad');

        $form = $crawler->selectButton('Laisse un Commentaire')->form();

        $form['comment[contenu]'] = "";
 
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testAddVideo()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/5');

        $form = $crawler->selectButton('Ajouter une vidéo')->form();

        $form['video[url]'] = "https://www.youtube.com/watch?v=SA7AIQw-7Ms";
 
        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(3, $crawler->filter('div.my-media-content.my-video')->count());
    }

    public function testAddVideoBad()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/5');

        $form = $crawler->selectButton('Ajouter une vidéo')->form();

        $form['video[url]'] = "https://www.google.com";
 
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testAddImage()
    {
        $imageGood = new UploadedFile(
            __DIR__.'/../../public/test-file/test-img.jpg',
            'image.jpg',
            'image/jpeg',
            123,
            true
        );

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/6');

        $form = $crawler->selectButton('Ajouter une image')->form();

        $form['picture[file]'] = $imageGood;
 
        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(4, $crawler->filter('div.my-media-content.my-img')->count());
    }

    public function testAddImageBad()
    {
        $imageBadType = new UploadedFile(
            __DIR__.'/../../public/test-file/text.txt',
            'text.txt',
            'text/plain',
            123,
            true
        );

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/5');

        $form = $crawler->selectButton('Ajouter une image')->form();

        $form['picture[file]'] = $imageBadType;
 
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
    }

    public function testAddImageEmpty()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'perso',
            'PHP_AUTH_PW'   => 'password'
        ));

        $crawler = $client->request('GET', '/edit-trick/5');

        $form = $crawler->selectButton('Ajouter une image')->form();

        $form['picture[file]'] = "";
 
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('div.invalid-feedback')->count());
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
            ['/load-more-trick', 200],
            ['/trick/sad',200]
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
            ['/remove-picture/7',302],
            ['/remove-video/3',302],
            ['/trick/sad',200]
        ];
    }

    public function badFormData()
    {
        return [
            [
                "name" => "",
                "description" => ""
            ],
            [
                "name" => "Un nom",
                "description" => ""
            ],
            [
                "name" => "",
                "description" => "Une description"
            ],
            [
                "name" => "180", // Nom déjà utilisé
                "description" => "Une description"
            ]
        ];
    }
}
