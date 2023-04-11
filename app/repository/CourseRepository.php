<?php

namespace repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Course;

require_once 'src/util/config.php';

class CourseRepository extends EntityRepository
{

    public function create($name, $details, $topic, $startingDate, $price): Course
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
    public function findById($id): ?Course
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :id')
            ->setParameter('id', $id);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findCourseByName($name): ?Course
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name')
            ->setParameter('name', $name);
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }

    public function findCoursesByTopic($topic): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.topic = :topic')
            ->setParameter('topic', $topic);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function update(array $data, $id): ?Course
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