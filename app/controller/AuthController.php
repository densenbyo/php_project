<?php

namespace controller;

use Exception;
use service\AuthService;

class AuthController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws Exception
     */
    public function userRegistration()
    {
        // Start session
        session_start();

        // Sanitize input
        $username = $this->sanitizeInput($_POST['username']);
        $email = $this->sanitizeInput($_POST['email']);
        $password = $this->sanitizeInput($_POST['password']);

        $user = $this->authService->userRegistration($username, $email, $password);

        if ($user) {
            $csrfToken = $this->generateCSRFToken();
            // Store user info in the session and return success response
            $_SESSION['user'] = $user;
            $_SESSION['user_type'] = 'user';
            echo json_encode(['status' => 'success', 'csrf_token' => $csrfToken]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists.']);
        }
    }

    /**
     * @throws Exception
     */
    public function teacherRegistration()
    {
        // Start session
        session_start();

        // Sanitize input
        $username = $this->sanitizeInput($_POST['username']);
        $email = $this->sanitizeInput($_POST['email']);
        $password = $this->sanitizeInput($_POST['password']);

        $teacher = $this->authService->teacherRegistration($username, $email, $password);

        if ($teacher) {
            $csrfToken = $this->generateCSRFToken();
            // Store teacher info in the session and return success response
            $_SESSION['user'] = $teacher;
            $_SESSION['user_type'] = 'teacher';
            echo json_encode(['status' => 'success', 'csrf_token' => $csrfToken]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists.']);
        }
    }

    /**
     * @throws Exception
     */
    public function login()
    {
        // Start session
        session_start();

        // Sanitize input
        $username = $this->sanitizeInput($_POST['username']);
        $password = $this->sanitizeInput($_POST['password']);
        $userType = $this->sanitizeInput($_POST['user_type']);

        if ($userType === 'user') {
            $loggedIn = $this->authService->userLogin($username, $password);
        } elseif ($userType === 'teacher') {
            $loggedIn = $this->authService->teacherLogin($username, $password);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user type.']);
            return;
        }

        if ($loggedIn) {
            $csrfToken = $this->generateCSRFToken();
            // Store user info in the session and return success response
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $userType;
            echo json_encode(['status' => 'success', 'csrf_token' => $csrfToken]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    }

    /**
     * @throws Exception
     */
    private function generateCSRFToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}