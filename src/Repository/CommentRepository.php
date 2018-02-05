<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getPaginateListOfCommentByFigure(Figure $figure, $pagination, $page = 1)
    {
        return $this->createQueryBuilder('f')
            ->where('f.figure = :figure')
            ->orderBy('f.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $pagination)
            ->setMaxResults($pagination)
            ->setParameter('figure', $figure)
            ->getQuery()
            ->getResult()
        ;
    }
}
