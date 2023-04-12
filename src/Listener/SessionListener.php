<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class SessionListener
{
    private $session;
    private $router;

    public function __construct(SessionInterface $session, RouterInterface $router)
    {
        $this->session = $session;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        if (in_array($routeName, ['user_login', 'teacher_login', 'user_register', 'teacher_register'])) {
            return;
        }

        $route = $this->router->getRouteCollection()->get($routeName);
        $requiredUserType = $route->getOption('secured');

        if ($requiredUserType && (!$this->session->has('user') || $this->session->get('user_type') !== $requiredUserType)) {
            $event->setResponse(new JsonResponse(['error' => 'Unauthorized'], 401));
        }
    }
}
