<?php

namespace controller;

use Exception;
use service\UserService;

class UserController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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

        $userData = json_decode(file_get_contents('php://input'), true);
        $sanitizedUserData = $this->sanitizeArray($userData);

        try {
            $createdUser = $this->userService->createUser(
                $sanitizedUserData['username'],
                $sanitizedUserData['email'],
                $sanitizedUserData['password']
            );
            header('Content-Type: application/json');
            echo json_encode($createdUser->toArray());
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
            $user = $this->userService->findById($sanitizedId);
            $this->userService->deleteUser($user);
            http_response_code(204);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getOwnCoursesAction($id)
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $courses = $this->userService->getOwnCourses($sanitizedId);
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($course) {
                return $course->toArray();
            }, $courses->toArray()));
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getFavCoursesAction($id)
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $courses = $this->userService->getFavCourses($sanitizedId);
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($course) {
                return $course->toArray();
            }, $courses->toArray()));
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

        $userData = json_decode(file_get_contents('php://input'), true);
        $sanitizedUserData = $this->sanitizeArray($userData);
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $updatedUser = $this->userService->update($sanitizedId, $sanitizedUserData);
            header('Content-Type: application/json');
            echo json_encode($updatedUser->toArray());
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