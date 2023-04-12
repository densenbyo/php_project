<?php

namespace App\Controller;

use Exception;
use App\Service\LectureService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LectureController extends AbstractController
{
    private $lectureService;

    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    /**
     * @Route("/lectures/create", methods={"POST"}, options={"secured": "teacher"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('create_lecture', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $lectureData = json_decode($request->getContent(), true);
        $sanitizedLectureData = $this->sanitizeArray($lectureData);

        try {
            $createdLecture = $this->lectureService->createLecture(
                $sanitizedLectureData['name'],
                $sanitizedLectureData['content']
            );
            return new JsonResponse($createdLecture->toArray(), 201);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/lectures/{id}/delete", methods={"DELETE"}, options={"secured": "teacher"})
     */
    public function deleteAction(int $id, Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('delete_lecture', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $sanitizedId = $this->sanitizeInput($id);

        try {
            $lecture = $this->lectureService->findById($sanitizedId);
            $this->lectureService->deleteLecture($lecture);
            return new JsonResponse(null, 204);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/lectures/{id}", methods={"GET"})
     */
    public function getByIdAction(int $id): JsonResponse
    {
        $sanitizedId = $this->sanitizeInput($id);

        try {
            $lecture = $this->lectureService->findById($sanitizedId);
            return new JsonResponse($lecture ? $lecture->toArray() : []);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/lectures", methods={"GET"})
     */
    public function getAllAction(): JsonResponse
    {
        try {
            $lectures = $this->lectureService->findAll();
            return new JsonResponse(array_map(function ($lecture) {
                return $lecture->toArray();
            }, $lectures));
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/lectures/name/{name}", methods={"GET"})
     */
    public function getByNameAction(string $name): JsonResponse
    {
        $sanitizedName = $this->sanitizeInput($name);

        try {
            $lecture = $this->lectureService->findByName($sanitizedName);
            return new JsonResponse($lecture ? $lecture->toArray() : []);
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

    private function sanitizeInput($input): string
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}
