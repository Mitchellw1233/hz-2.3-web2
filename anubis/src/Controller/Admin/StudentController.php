<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\ExamRegistration;
use App\Entity\Student;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;
use Slimfony\Validation\Constraint;
use Slimfony\Validation\Exception\ValidationException;
use Slimfony\Validation\Validator;

class StudentController extends AbstractAdminController
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
            'firstname' => new Constraint('string'),
            'lastname' => new Constraint('string'),
            'email' => new Constraint('string'),
            'birth_date' => new Constraint('string'),
            'exam_ids' => new Constraint('integer', nullable: true, empty: true),
        ];
    }

    public function list(): Response
    {
        return $this->render('pages/admin/student/list.php', [
            'students' => $this->entityManager->getQueryBuilder(Student::class)->result(),
        ]);
    }

    public function single(int $id): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.student.single', ['id' => $id], ['edit' => 'true', 'errors' => $e->getMessage()]);
            }

            $student = $this->entityManager->persist($this->updateStudentFromRequest($id, $data));
            $this->persistRegistrationsFromRequest($data, $student);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.student.single', ['id' => $student->getId()]);
        }

        return $this->render('pages/admin/student/crud.php', [
            'student' => $this->entityManager->getQueryBuilder(Student::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result(),
            'registrations' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->result(),
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
            'editable' => $this->getRequest()->getQuery()->get('edit') === 'true',
            'errors' => $this->getRequest()->getQuery()->get('errors'),
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.student.create', [], ['errors' => $e->getMessage()]);
            }

            $student = $this->entityManager->persist($this->newStudentFromRequest($data));
            $this->persistRegistrationsFromRequest($data, $student);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.student.single', ['id' => $student->getId()]);
        }

        return $this->render('pages/admin/student/crud.php', [
            'student' => null,
            'registrations' => null,
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
            'editable' => true,
            'errors' => $this->getRequest()->getQuery()->get('errors'),
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        $this->entityManager->remove(
            $this->entityManager->getQueryBuilder(Student::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result()
        );
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.student.list');
    }

    private function updateStudentFromRequest(int $id, array $data): Student
    {
        /** @var Student $student */
        $student = $this->entityManager->getQueryBuilder(Student::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->limit(1)
            ->result();

        $student->setFirstName($data['firstname']);
        $student->setLastName($data['lastname']);
        $student->setEmail($data['email']);
        $student->setBirthDate(new \DateTime($data['birth_date']));

        return $student;
    }

    private function newStudentFromRequest(array $data): Student
    {
        return new Student(
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            'defaultpassword',
            new \DateTime($data['birth_date'])
        );
    }

    /**
     * @param array<string, mixed> $requestData
     * @param Student $student
     *
     * @return ExamRegistration[]
     */
    private function persistRegistrationsFromRequest(array $requestData, Student $student): array
    {
        $registrations = [];

        /** @var ExamRegistration[] $prevRegistrations */
        $prevRegistrations = $this->entityManager->getQueryBuilder(ExamRegistration::class)
            ->where('student_id = :student_id')
            ->setParameters([
                'student_id' => $student->getId(),
            ])
            ->result();

        if (!isset($requestData['exam_ids'])) {
            $requestData['exam_ids'] = [];
        }

        if (!is_array($requestData['exam_ids'])) {
            $requestData['exam_ids'] = [$requestData['exam_ids']];
        }

        foreach ($prevRegistrations as $prevRegistration) {
            if (!in_array($prevRegistration->getExam()->getId(), $requestData['exam_ids'])) {
                $this->entityManager->remove($prevRegistration);
            } else {
                $i = array_search($prevRegistration->getExam()->getId(), $requestData['exam_ids']);
                unset($requestData['exam_ids'][$i]);
                $registrations[] = $prevRegistration;
            }
        }

        if (empty($requestData['exam_ids'])) {
            return $registrations;
        }

        foreach ($requestData['exam_ids'] as $examId) {
            if ($examId === '-1') {
                continue;
            }

            $exam = $this->entityManager->getQueryBuilder(Exam::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $examId,
                ])
                ->limit(1)
                ->result();

            $registrations[] = $this->entityManager->persist(
                new ExamRegistration($exam, $student, new \DateTime('now'), null, null));
        }

        return $registrations;
    }
}
