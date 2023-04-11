<?php

namespace controller;

use Exception;
use service\LectureService;

class LectureController
{
    private $lectureService;

    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
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

        $lectureData = json_decode(file_get_contents('php://input'), true);
        $sanitizedLectureData = $this->sanitizeArray($lectureData);

        try {
            $createdLecture = $this->lectureService->createLecture(
                $sanitizedLectureData['name'],
                $sanitizedLectureData['content']
            );
            header('Content-Type: application/json');
            echo json_encode($createdLecture->toArray());
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
            $lecture = $this->lectureService->findById($sanitizedId);
            $this->lectureService->deleteLecture($lecture);
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
            $lecture = $this->lectureService->findById($sanitizedId);
            header('Content-Type: application/json');
            echo json_encode($lecture ? $lecture->toArray() : []);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAllAction()
    {
        try {
            $lectures = $this->lectureService->findAll();
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($lecture) {
                return $lecture->toArray();
            }, $lectures));
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getByNameAction($name)
    {
        $sanitizedName = $this->sanitizeInput($name);

        try {
            $lecture = $this->lectureService->findByName($sanitizedName);
            header('Content-Type: application/json');
            echo json_encode($lecture ? $lecture->toArray() : []);
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