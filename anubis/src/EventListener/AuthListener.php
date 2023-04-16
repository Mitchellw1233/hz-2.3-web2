<?php

namespace App\EventListener;

use App\Entity\Admin;
use App\Entity\Interface\UserInterface;
use App\Entity\Student;
use App\Entity\Teacher;
use Slimfony\EventDispatcher\EventSubscriberInterface;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Request;
use Slimfony\HttpKernel\Event\PreViewEvent;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\Routing\RouteResolver;

class AuthListener implements EventSubscriberInterface
{
    public function __construct(
        protected RouteResolver $routeResolver,
    ) {
    }

    public const PERMISSIONS = [
        'admin.' => [Admin::class],
        'admin.exam.list' => [Teacher::class],
        'admin.exam.single' => [Teacher::class],
        'student.' => [Student::class],
        'auth.logout' => [Student::class, Teacher::class, Admin::class],
        'auth.profile' => [Student::class, Teacher::class, Admin::class],
        'home' => [Student::class, Teacher::class, Admin::class],
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            PreViewEvent::class => [
                ['onPreView'],
            ],
        ];
    }

    public function onPreView(PreViewEvent $e): void
    {
        $name = $e->getRoute()->getName();
        $user = $this->getUserFromRequest($e->getRequest());

        if ($user === null && $name !== 'auth.login') {
            $e->setResponse(new RedirectResponse($e->getRequest()->getUri()->getBase()
                . $this->routeResolver->resolveRouteByName('auth.login')->buildPath()));
            return;
        } else if ($user === null) {
            return;
        }

        foreach (self::PERMISSIONS as $routeName => $permClasses) {
            foreach ($permClasses as $permClass) {
                if (str_starts_with($name, $routeName) && is_a($user, $permClass)) {
                    return;
                }
            }
        }

        throw new ForbiddenException();
    }

    protected function getUserFromRequest(Request $request): ?UserInterface
    {
        if (!$request->getSession()->has('_user')) {
            return null;
        }

        return unserialize($request->getSession()->get('_user'),
            ['allowed_classes' => [Student::class, Teacher::class, Admin::class]]);
    }
}