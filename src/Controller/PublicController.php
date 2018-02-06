<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Figure;
use App\Form\CommentType;
use App\Entity\Comment;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FigureType;

class PublicController extends Controller
{
    public function index()
    {
        $pagination = $this->getParameter('pagination-trick');

        $em = $this->getDoctrine()->getManager();

        $figures = $em->getRepository(Figure::class)->getPaginateListOfTricks($pagination);

        return $this->render('index.html.twig', ['figures' => $figures]);
    }

    public function oneTrick(Figure $figure, Request $request)
    {
        $pagination = $this->getParameter('pagination-comment');

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

    public function addTrick(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trick = new Figure();

        $form = $this->createForm(FigureType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('trick', ['slug'=> $trick->getSlug()]);
        }

        return $this->render('add-tricks.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function editTrick(Figure $figure, Request $request)
    {
    }

    public function removeTrick(Figure $figure)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($figure);
        $em->flush();

        return $this->redirectToRoute('index');
    }

    public function loadlistComment(Figure $figure, $page)
    {
        $pagination = $this->getParameter('pagination-comment');

        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository(Comment::class)->getPaginateListOfCommentByFigure($figure, $pagination, $page);

        return $this->render('list-comments.html.twig', ['comments'=> $comments]);
    }

    public function loadListTrick($page)
    {
        $pagination = $this->getParameter('pagination-trick');

        $em = $this->getDoctrine()->getManager();

        $figures = $em->getRepository(Figure::class)->getPaginateListOfTricks($pagination, $page);

        return $this->render('list-trick.html.twig', ['figures'=> $figures]);
    }
}
