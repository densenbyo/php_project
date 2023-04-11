<?php

namespace service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use model\Comments;
use repository\CommentsRepository;

require_once 'src/repository/CommentsRepository.php';

class CommentsService
{
    private $commentRepo;

    public function __construct(CommentsRepository $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    public function createComment($comment): Comments
    {
        // Add any validation or business logic here

        return $this->commentRepo->create($comment);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteComment($id)
    {
        // Add any validation or business logic here

        $comment = $this->commentRepo->findById($id);
        $this->commentRepo->delete($comment);
    }

    public function getAllComments(): array
    {
        // Add any validation or business logic here

        return $this->commentRepo->findAll();
    }
}