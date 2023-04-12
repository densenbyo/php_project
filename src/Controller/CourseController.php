<?php

namespace App\Controller;

use App\Service\CourseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    private $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * @Route("/course/create", name="create_course", methods={"POST"}, options={"secured": "teacher"})
     */
    public function createAction(Request $request): JsonResponse
    {
        $courseData = json_decode($request->getContent(), true);
        $sanitizedCourseData = $this->sanitizeArray($courseData);

        try {
            $createdCourse = $this->courseService->createCourse(
                $sanitizedCourseData['name'],
                $sanitizedCourseData['details'],
                $sanitizedCourseData['topic'],
                $sanitizedCourseData['startingDate'],
                $sanitizedCourseData['price']
            );

            return new JsonResponse($createdCourse->toArray(), JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/course/delete/{id}", name="delete_course", methods={"DELETE"}, options={"secured": "teacher"})
     */
    public function deleteAction(int $id): JsonResponse
    {
        try {
            $this->courseService->deleteCourse($id);
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/course/all", name="find_all_courses", methods={"GET"})
     */
    public function findAllCourses(): JsonResponse
    {
        $courseArr = $this->courseService->getAll();
        return new JsonResponse($courseArr);
    }

    /**
     * @Route("/course/find/name/{name}", name="find_by_name", methods={"GET"})
     */
    public function findByNameAction(string $name): JsonResponse
    {
        $sanitizedName = $this->sanitizeInput($name);

        try {
            $course = $this->courseService->findByName($sanitizedName);
            if ($course != null) {
                return new JsonResponse($course->toArray());
            } else {
                return new JsonResponse([]);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/course/find/topic/{topic}", name="find_by_topic", methods={"GET"})
     */
    public function findByTopicAction(string $topic): JsonResponse
    {
        $sanitizedTopic = $this->sanitizeInput($topic);

        try {
            $courses = $this->courseService->findByTopic($sanitizedTopic);
            $coursesArray = array_map(function ($course) {
                return $course->toArray();
            }, $courses);

            return new JsonResponse($coursesArray);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/course/update/{id}", name="update_course", methods={"PUT"})
     */
    public function updateAction(int $id, Request $request): JsonResponse
    {
        $courseData = json_decode($request->getContent(), true);
        $sanitizedCourseData = $this->sanitizeArray($courseData);
        try {
            $updatedCourse = $this->courseService->updateCourse($id, $sanitizedCourseData);
            return new JsonResponse($updatedCourse->toArray());
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
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