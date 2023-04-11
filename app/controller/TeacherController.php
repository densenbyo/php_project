<?php

namespace controller;

use Exception;
use service\TeacherService;

class TeacherController
{
    private $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
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

        $teacherData = json_decode(file_get_contents('php://input'), true);
        $sanitizedTeacherData = $this->sanitizeArray($teacherData);

        try {
            $createdTeacher = $this->teacherService->createTeacher(
                $sanitizedTeacherData['username'],
                $sanitizedTeacherData['email'],
                $sanitizedTeacherData['password']
            );
            header('Content-Type: application/json');
            echo json_encode($createdTeacher->toArray());
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
            $teacher = $this->teacherService->findById($sanitizedId);
            $this->teacherService->deleteTeacher($teacher);
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
            $teacher = $this->teacherService->findById($sanitizedId);
            header('Content-Type: application/json');
            echo json_encode($teacher ? $teacher->toArray() : []);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function findByUsernameAction($username)
    {
        $sanitizedUsername = $this->sanitizeInput($username);

        try {
            $teacher = $this->teacherService->findByUsername($sanitizedUsername);
            header('Content-Type: application/json');
            echo json_encode($teacher ? $teacher->toArray() : []);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function findAllAction()
    {
        try {
            $teachers = $this->teacherService->findAll();
            header('Content-Type: application/json');
            echo json_encode(array_map(function ($teacher) {
                return $teacher->toArray();
            }, $teachers));
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
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
}