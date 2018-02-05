<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Figure;
use App\Form\CommentType;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends Controller
{
    public function index()
    {
        $pagination = 15;

        $em = $this->getDoctrine()->getManager();

        $figures = $em->getRepository(Figure::class)->getPaginateListOfTricks($pagination);

        return $this->render('index.html.twig', ['figures' => $figures]);
    }

    public function oneTrick(Figure $figure, Request $request)
    {
        $pagination = 15;

        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository(Comment::class)->getPaginateListOfCommentByFigure($figure, $pagination);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $user = $this->getUser();

        if (null != $user && $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setFigure($figure);
                $comment->setAuhtor($user);

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('trick', ['slug' => $figure->getSlug()]);
            }
        }

        return $this->render('trick.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}
