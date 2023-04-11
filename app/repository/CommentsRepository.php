<?php

namespace repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Comments;

require_once 'src/util/config.php';

class CommentsRepository extends EntityRepository
{

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
     * @throws NoResultException
     */
    public function findById($id): ?Comments
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function delete(Comments $user): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }

    public function findAllComments(): array
    {
        $qb = $this->createQueryBuilder('c');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }
}