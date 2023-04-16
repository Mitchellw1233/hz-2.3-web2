<?php

namespace App\Controller\Student;

use App\Entity\ExamRegistration;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\ORM\Query\OrderByEnum;
use Slimfony\Routing\RouteResolver;

class GradeController extends AbstractStudentController
{
    public function __construct(
        Container $container,
        RouteResolver $routeResolver,
        protected EntityManager $entityManager
    )
    {
        parent::__construct($container, $routeResolver);
    }

    public function list(): Response
    {
        $studentId = $this->getUser()->getId();

        return $this->render('pages/student/grade/list.php', [
            'grades' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :student_id AND grade IS NOT NULL')
                ->orderBy(OrderByEnum::DESC, 'graded_at')
                ->setParameters([
                    'student_id' => $studentId,
                ])
                ->result(),
        ]);
    }
}