<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;


class CommentsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function create($comment): Comments
    {
        $em = $this->getEntityManager();
        $comment = new Comments($comment);
        $em -> persist($comment);
        $em -> flush();
        return $comment;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById($id): ?Comments
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->from('App:Comments','c')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function delete(Comments $user): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }

    public function findAllComments(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c')->select('c')
            ->from('App:Comments','c');
        return $qb->getQuery()->getArrayResult();
    }
}