<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Service\CommentsService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    private $commentsService;

    public function __construct(CommentsService $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    /**
     * @Route("/comment/create", name="create_comment", methods={"POST"}, options={"secured": "user"})
     */
    public function createAction(Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('create_comment', $request->request->get('csrf_token'))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $commentData = json_decode($request->getContent(), true);
        $sanitizedCommentData = $this->sanitizeArray($commentData);

        try {
            $comment = new Comments($sanitizedCommentData['comments']);
            $createdComment = $this->commentsService->createComment($comment);
            return new JsonResponse($createdComment->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/comment/delete/{id}", name="delete_comment", methods={"DELETE"}, options={"secured": "user"})
     */
    public function deleteAction(Request $request, int $id): JsonResponse
    {
        if (!$this->isCsrfTokenValid('delete_comment', $request->request->get('csrf_token'))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->commentsService->deleteComment($id);
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/comment/all", name="get_all_comments", methods={"GET"})
     */
    public function getAllAction(): JsonResponse
    {
        try {
            $comments = $this->commentsService->getAllComments();
            $commentsArray = array_map(function ($comment) {
                return $comment->toArray();
            }, $comments);
            return new JsonResponse($commentsArray);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function sanitizeArray(array $data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? $this->sanitizeArray($item) : $this->sanitizeInput($item);
        }, $data);
    }

    private function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}
