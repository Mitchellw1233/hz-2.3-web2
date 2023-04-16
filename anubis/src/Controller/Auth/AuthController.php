<?php

namespace App\Controller\Auth;

use App\Controller\AbstractBaseController;
use App\Entity\Admin;
use App\Entity\Interface\UserInterface;
use App\Entity\Student;
use App\Entity\Teacher;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\Response;
use Slimfony\ORM\Entity;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;
use Slimfony\Validation\Constraint;
use Slimfony\Validation\Exception\ValidationException;
use Slimfony\Validation\Validator;

class AuthController extends AbstractBaseController
{
    public function __construct(
        Container $container,
        RouteResolver $routeResolver,
        protected EntityManager $entityManager
    )
    {
        parent::__construct($container, $routeResolver);
    }

    public static function validationSchema(): array
    {
        return [
            'role' => new Constraint('string'),
            'email' => new Constraint('string'),
            'password' => new Constraint('string'),
        ];
    }

    public function login(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $this->setUser($this->getUserFromRequest());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('auth.login', [], ['errors' => $e->getMessage()]);
            }

            return $this->redirectToRoute('home');
        }

        if ($this->getUser() !== null) {
            return $this->redirectToRoute('home');
        }

        return $this->render('pages/auth/login.php', [
            'errors' => $this->getRequest()->getQuery()->get('errors'),
        ]);
    }

    public function logout(): Response
    {
        $this->setUser(null);

        return $this->redirectToRoute('auth.login');
    }

    public function profile(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $data = Validator::validate($this->getRequest()->request->all(), ['password' => self::validationSchema()['password']]);
            } catch (ValidationException $e) {
                return $this->redirectToRoute('auth.profile', [], ['errors' => $e->getMessage()]);
            }

            /** @var Entity|UserInterface $user */
            $user = $this->getUser();
            $user->setPassword($data['password']);
            $this->setUser($this->entityManager->persist($user));
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('pages/auth/profile.php', [
            'user' => $this->getUser(),
            'errors' => $this->getRequest()->getQuery()->get('errors'),
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function getUserFromRequest(): UserInterface
    {
        $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());
        /** @var UserInterface $user */
        $user = $this->entityManager->getQueryBuilder(match ($data['role']) {
            'student' => Student::class,
            'teacher' => Teacher::class,
            'admin' => Admin::class,
            default => throw new ValidationException('Role is not allowed'),
        })->where('email = :email')
            ->setParameters([
                'email' => $data['email'],
            ])
            ->limit(1)
            ->result() ?? throw new ValidationException('User cannot be found');

        if (!$user->verifyPassword($data['password'])) {
            throw new ValidationException('Password does not match');
        }

        return $user;
    }
}