<?php

namespace App\Controller;

use Exception;
use App\Service\TeacherService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{
    private $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * @Route("/teachers/create", methods={"POST"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isValidCsrfToken($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $teacherData = json_decode($request->getContent(), true);
        $sanitizedTeacherData = $this->sanitizeArray($teacherData);

        try {
            $createdTeacher = $this->teacherService->createTeacher(
                $sanitizedTeacherData['username'],
                $sanitizedTeacherData['email'],
                $sanitizedTeacherData['password']
            );
            return new JsonResponse($createdTeacher->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/teachers/{id}/delete", methods={"DELETE"}, options={"secured": "teacher"})
     */
    public function deleteAction(int $id, Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isValidCsrfToken($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $sanitizedId = $this->sanitizeInput((string) $id);

        try {
            $teacher = $this->teacherService->findById($sanitizedId);
            $this->teacherService->deleteTeacher($teacher);
            return new JsonResponse(null, 204);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/teachers/{id}", methods={"GET"})
     */
    public function getByIdAction(int $id): JsonResponse
    {
        $sanitizedId = $this->sanitizeInput((string) $id);

        try {
            $teacher = $this->teacherService->findById($sanitizedId);
            return new JsonResponse($teacher ? $teacher->toArray() : []);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/teachers/username/{username}", methods={"GET"})
     */
    public function findByUsernameAction(string $username): JsonResponse
    {
        $sanitizedUsername = $this->sanitizeInput($username);

        try {
            $teacher = $this->teacherService->findByUsername($sanitizedUsername);
            return new JsonResponse($teacher ? $teacher->toArray() : []);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/teachers", methods={"GET"})
     */
    public function findAllAction(): JsonResponse
    {
        try {
            $teachers = $this->teacherService->findAll();
            return new JsonResponse(array_map(function ($teacher) {
                return $teacher->toArray();
            }, $teachers));
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/teacher/update/{id}", name="update_teacher", methods={"PUT"})
     */
    public function updateAction(int $id, Request $request): JsonResponse
    {
        $courseData = json_decode($request->getContent(), true);
        $sanitizedCourseData = $this->sanitizeArray($courseData);
        try {
            $updatedCourse = $this->teacherService->update($id, $sanitizedCourseData);
            return new JsonResponse($updatedCourse->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    private function sanitizeArray(array $data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? $this->sanitizeArray($item) : $this->sanitizeInput($item);
        }, $data);
    }

    private function isValidCsrfToken(?string $token): bool
    {
        return $token && $this->isCsrfTokenValid('teacher', $token);
    }
}

