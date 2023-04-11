<?php

namespace controller;

use Exception;
use service\ReviewService;

class ReviewController
{
    private $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
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

        $reviewData = json_decode(file_get_contents('php://input'), true);
        $sanitizedReviewData = $this->sanitizeArray($reviewData);

        try {
            $createdReview = $this->reviewService->createReview(
                $sanitizedReviewData['rating'],
                $sanitizedReviewData['message']
            );
            header('Content-Type: application/json');
            echo json_encode($createdReview->toArray());
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
            $review = $this->reviewService->findById($sanitizedId);
            $this->reviewService->deleteReview($review);
            http_response_code(204);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getByIdAction($id)
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $review = $this->reviewService->findById($sanitizedId);
            header('Content-Type: application/json');
            echo json_encode($review ? $review->toArray() : []);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
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