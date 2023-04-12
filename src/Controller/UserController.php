<?php

namespace App\Controller;

use Exception;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/user/create", methods={"POST"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('user', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $userData = json_decode($request->getContent(), true);
        $sanitizedUserData = $this->sanitizeArray($userData);

        try {
            $createdUser = $this->userService->createUser(
                $sanitizedUserData['username'],
                $sanitizedUserData['email'],
                $sanitizedUserData['password']
            );
            return new JsonResponse($createdUser->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/user/delete/{id}", methods={"POST"})
     */
    public function deleteAction(Request $request, $id): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('user', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $sanitizedId = $this->sanitizeInput($id);

        try {
            $user = $this->userService->findById($sanitizedId);
            $this->userService->deleteUser($user);
            return new JsonResponse(null, 204);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/user/{id}/courses", methods={"GET"}, options={"secured": "user"})
     */
    public function getOwnCoursesAction($id): JsonResponse
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $courses = $this->userService->getOwnCourses($sanitizedId);
            return new JsonResponse(array_map(function ($course) {
                return $course->toArray();
            }, $courses->toArray()));
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/user/{id}/favorites", methods={"GET"}, options={"secured": "user"})
     */
    public function getFavCoursesAction($id): JsonResponse
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $courses = $this->userService->getFavCourses($sanitizedId);
            return new JsonResponse(array_map(function ($course) {
                return $course->toArray();
            }, $courses->toArray()));
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/user/update/{id}", methods={"PUT"}, options={"secured": "user"})
     */
    public function updateAction(Request $request, $id): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('user', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $userData = json_decode($request->getContent(), true);
        $sanitizedUserData = $this->sanitizeArray($userData);
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $updatedUser = $this->userService->update($sanitizedId, $sanitizedUserData);
            return new JsonResponse($updatedUser->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function sanitizeArray(array $data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? $this->sanitizeArray($item) : $this->sanitizeInput($item);
        }, $data);
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}
