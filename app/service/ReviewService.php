<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Review;
use repository\ReviewRepository;

require_once 'src/repository/ReviewRepository.php';
class ReviewService
{
    private $courseRepo;

    public function __construct(ReviewRepository $commentRepo)
    {
        $this->courseRepo = $commentRepo;
    }

    public function createReview($rating, $message): Review
    {
        return $this->courseRepo->create($rating, $message);
    }

    public function deleteReview(Review $review): void
    {
        $this->courseRepo->delete($review);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findById($id): ?Review
    {
        return $this->courseRepo->findById($id);
    }
}