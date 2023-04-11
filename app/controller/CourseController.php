<?php

namespace controller;

use Exception;
use service\CourseService;

class CourseController
{
    private $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
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

        $courseData = json_decode(file_get_contents('php://input'), true);
        $sanitizedCourseData = $this->sanitizeArray($courseData);

        try {
            $createdCourse = $this->courseService->createCourse(
                $sanitizedCourseData['name'],
                $sanitizedCourseData['details'],
                $sanitizedCourseData['topic'],
                $sanitizedCourseData['startingDate'],
                $sanitizedCourseData['price']
            );
            header('Content-Type: application/json');
            echo json_encode($createdCourse->toArray());
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
            $this->courseService->deleteCourse($sanitizedId);
            http_response_code(204);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function findByNameAction($name)
    {
        $sanitizedName = $this->sanitizeInput($name);

        try {
            $course = $this->courseService->findByName($sanitizedName);
            header('Content-Type: application/json');
            echo json_encode($course ? $course->toArray() : []);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function findByTopicAction($topic)
    {
        $sanitizedTopic = $this->sanitizeInput($topic);

        try {
            $courses = $this->courseService->findByTopic($sanitizedTopic);
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($course) {
                return $course->toArray();
            }, $courses));
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updateAction($id)
    {
        session_start();

        $csrfToken = $_POST['csrf_token'];
        if (!$this->isValidCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $sanitizedId = $this->sanitizeInput($id);
        $courseData = json_decode(file_get_contents('php://input'), true);
        $sanitizedCourseData = $this->sanitizeArray($courseData);

        try {
            $updatedCourse = $this->courseService->updateCourse($sanitizedId, $sanitizedCourseData);
            header('Content-Type: application/json');
            echo json_encode($updatedCourse->toArray());
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