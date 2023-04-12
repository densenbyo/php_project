<?php

namespace App\Repository;


use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;


class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function create(string $name, string $details, string $topic, \DateTime $startingDate, int $price): Course
    {
        $em = $this->getEntityManager();
        $course = new Course($name, $details, $topic, $startingDate, $price);
        $em -> persist($course);
        $em -> flush();
        return $course;
    }

    public function delete(Course $user): void
    {
        $em = $this->getEntityManager();
        $em -> remove($user);
        $em -> flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById(int $id): ?Course
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->from('App:Course','c')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function findCourseByName(string $name): ?Course
    {
        try {
            $qb = $this->getEntityManager()->createQueryBuilder('c')
                ->select('c')
                ->where('c.name = :name')
                ->from('App:Course','c')
                ->setParameter('name', $name);
            $query = $qb->getQuery();
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findCoursesByTopic(string $topic): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c')
            ->select('c')
            ->where('c.topic = :topic')
            ->from('App:Course','c')
            ->setParameter('topic', $topic);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAll(): array
    {

        $qb = $this->getEntityManager()->createQueryBuilder('c')->select('c')
            ->from('App:Course','c');
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function update(array $data, int $id): ?Course
    {
        $em = $this->getEntityManager();
        $course =  $this->findById($id);

        if (isset($data['reviews'])) {
            $course -> setReviews($data['reviews']);
        }
        if (isset($data['lectures'])) {
            $course -> setLectures($data['lectures']);
        }
        if (isset($data['users'])) {
            $course -> setUsers($data['users']);
        }
        if (isset($data['comments'])) {
            $course -> setComments($data['comments']);
        }

        $em -> persist($course);
        $em -> flush();
        return $course;
    }
}