<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Entity\User;
use App\Service\AuthService;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthController extends AbstractController
{
    private $authService;
    private $session;

    public function __construct(AuthService $authService, SessionInterface $session)
    {
        $this->authService = $authService;
        $this->session = $session;
    }

    /**
     * @Route("/user/login", name="user_login", methods={"POST"})
     * @throws \Exception
     */
    public function userLogin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $this->sanitizeInput($data['username']);
        $password = $this->sanitizeInput($data['password']);

        $result = $this->authService->userLogin($username, $password);

        if ($result) {
            $user = $this->authService->getUser($username);
            $token = $this->generateCSRFToken();
            $this->session->set('user', $user);
            $this->session->set('user_type', 'user');
            return $this->json(['success' => true, 'csrf_token' => $token]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * @Route("/user/register", name="user_register", methods={"POST"})
     */
    public function userRegistration(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        $user = $this->authService->userRegistration($username, $email, $password);

        if ($user instanceof User) {
            return $this->json(['success' => true, 'user_id' => $user->getId()]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/teacher/login", name="teacher_login", methods={"POST"})
     * @throws \Exception
     */
    public function teacherLogin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $this->sanitizeInput($data['username']);
        $password = $this->sanitizeInput($data['password']);

        $result = $this->authService->teacherLogin($username, $password);

        if ($result) {
            $teacher = $this->authService->getTeacher($username);
            $token = $this->generateCSRFToken();
            $this->session->set('user', $teacher);
            $this->session->set('user_type', 'teacher');
            return $this->json(['success' => true, 'csrf_token' => $token]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * @Route("/teacher/register", name="teacher_register", methods={"POST"})
     */
    public function teacherRegistration(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        $teacher = $this->authService->teacherRegistration($username, $email, $password);

        if ($teacher instanceof Teacher) {
            return $this->json(['success' => true, 'teacher_id' => $teacher->getId()]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @throws Exception|\Exception
     */
    private function generateCSRFToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}
