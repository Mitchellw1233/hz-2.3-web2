<?php

namespace App\Controller\Student;

use App\Entity\Exam;
use App\Entity\ExamRegistration;
use App\Entity\Student;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;

class RegistrationController extends AbstractStudentController
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
        if (!$this->verify()) {
            throw new ForbiddenException();
        }

        // TODO: REMOVE
        $id = 5;
//        $id = $this->getUser()->getId();

        return $this->render('pages/student/registration/list.php', [
            'all' => $this->entityManager->getQueryBuilder(ExamRegistration::class)->result(),
            'registrations' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->result(),
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
        ]);
    }

    public function delete(ExamRegistration $registration): void
    {
        $this->entityManager->remove($registration);
    }

    public function register(Exam $exam): void
    {
        if (!$this->verify()) {
            throw new ForbiddenException();
        }

        // TODO: REMOVE
        $id = 5;
//        $id = $this->getUser()->getId();

        // TODO: REMOVE
        $student = $this->entityManager->getQueryBuilder(Student::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->limit(1)
            ->result();
//        $student = $this->getUser();

        $registration = new ExamRegistration(
            $exam,
            $student,
            new \DateTime('now'),
            null,
            null,
        );

        $this->entityManager->persist($registration);
        $this->entityManager->flush();
    }
}