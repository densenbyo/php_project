<?php

namespace controller;

use Exception;
use model\Comments;
use service\CommentsService;

class CommentController
{
    private $commentsService;

    public function __construct(CommentsService $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    public function createAction()
    {

        session_start();

        $csrfToken = $_POST['csrf_token'];
        if (!$this->isValidCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $commentData = json_decode(file_get_contents('php://input'), true);
        $sanitizedCommentData = $this->sanitizeArray($commentData);

        try {
            $comment = new Comments($sanitizedCommentData['comments']);
            $createdComment = $this->commentsService->createComment($comment);
            header('Content-Type: application/json');
            echo json_encode($createdComment->toArray());
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteAction($id)
    {
        session_start();

        $csrfToken = $_POST['csrf_token'];
        if (!$this->isValidCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $sanitizedId = $this->sanitizeInput($id);
        try {
            $this->commentsService->deleteComment($sanitizedId);
            http_response_code(204);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAllAction()
    {
        try {
            $comments = $this->commentsService->getAllComments();
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($comment) {
                return $comment->toArray();
            }, $comments));
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function sanitizeArray(array $data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? $this->sanitizeArray($item) : $this->sanitizeInput($item);
        }, $data);
    }

    private function isValidCsrfToken($token): bool
    {
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }

    private function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}