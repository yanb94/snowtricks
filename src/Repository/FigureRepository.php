<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FigureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    public function getPaginateListOfTricks($pagination, $page = 1)
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'ASC')
            ->setFirstResult(($page - 1) * $pagination)
            ->setMaxResults($pagination)
            ->getQuery()
            ->getResult()
        ;
    }
}
