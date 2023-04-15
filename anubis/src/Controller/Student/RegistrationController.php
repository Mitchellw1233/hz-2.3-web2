<?php

namespace App\Controller\Student;

use App\Controller\AbstractBaseController;
use App\Entity\ExamRegistration;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;

class RegistrationController extends AbstractBaseController
{
    public function __construct(
        Container $container,
        RouteResolver $routeResolver,
        protected EntityManager $entityManager
    )
    {
        parent::__construct($container, $routeResolver);
    }

    public function list()
    {
        if (!$this->verify()) {
            throw new ForbiddenException();
        }

        $studentId = $this->getUser()->getId();

        return $this->render('pages/student/registrations/list.php', [
            'grades' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :student_id')
                ->setParameters([
                    'student_id' => $studentId,
                ])
                ->result(),
        ]);
    }
}