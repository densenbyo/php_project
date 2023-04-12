<?php

namespace App\Controller;

use Exception;
use App\Service\ReviewService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    private $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @Route("/reviews/create", methods={"POST"}, options={"secured": "user"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isValidCsrfToken($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $reviewData = json_decode($request->getContent(), true);
        $sanitizedReviewData = $this->sanitizeArray($reviewData);

        try {
            $createdReview = $this->reviewService->createReview(
                $sanitizedReviewData['rating'],
                $sanitizedReviewData['message']
            );
            return new JsonResponse($createdReview->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/reviews/{id}/delete", methods={"DELETE"}, options={"secured": "user"})
     */
    public function deleteAction(int $id, Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isValidCsrfToken($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $sanitizedId = $this->sanitizeInput((string) $id);

        try {
            $review = $this->reviewService->findById($sanitizedId);
            $this->reviewService->deleteReview($review);
            return new JsonResponse(null, 204);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/reviews/{id}", methods={"GET"})
     */
    public function getByIdAction(int $id): JsonResponse
    {
        $sanitizedId = $this->sanitizeInput((string) $id);

        try {
            $review = $this->reviewService->findById($sanitizedId);
            return new JsonResponse($review ? $review->toArray() : []);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function isValidCsrfToken(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    private function sanitizeArray(array $data): array
    {
        return array_map([$this, 'sanitizeInput'], $data);
    }
}