<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller
{
    /**
     * @Route("/public", name="public")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
}
