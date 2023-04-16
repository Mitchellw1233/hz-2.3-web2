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
        $studentId = $this->getUser()->getId();

        return $this->render('pages/student/registration/list.php', [
            'registrations' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :id')
                ->setParameters([
                    'id' => $studentId,
                ])
                ->result(),
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
        ]);
    }

    public function delete(int $id): Response
    {
        $registration = $this->entityManager->getQueryBuilder(ExamRegistration::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id
            ])
            ->limit(1)
            ->result();
        $this->entityManager->remove($registration);
        $this->entityManager->flush();

        return $this->redirectToRoute('student.registration.list');
    }

    public function register(int $id): Response
    {
        /** @var Student $student */
        $student = $this->getUser();

        $exam = $this->entityManager->getQueryBuilder(Exam::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id
            ])
            ->limit(1)
            ->result();

        $registration = new ExamRegistration(
            $exam,
            $student,
            new \DateTime('now'),
            null,
            null,
        );

        $this->entityManager->persist($registration);
        $this->entityManager->flush();

        return $this->redirectToRoute('student.registration.list');
    }
}