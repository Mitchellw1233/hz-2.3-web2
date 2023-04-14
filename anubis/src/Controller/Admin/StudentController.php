<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\ExamRegistration;
use App\Entity\Student;
use App\Util\Validator;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\BadRequestException;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;

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

    public function list(): Response
    {
        if (!$this->verify()) {
            throw new ForbiddenException();
        }

        return $this->render('pages/admin/student/list.php', [
            'students' => $this->entityManager->getQueryBuilder(Student::class)->result(),
        ]);
    }

    public function single(int $id): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $student = $this->entityManager->persist($this->updateStudentFromRequest($id));
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
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $student = $this->newStudentFromRequest();
            return $this->redirectToRoute('admin.student.single', ['id' => $student->getId()]);
        }

        return $this->render('pages/admin/student/crud.php', [
            'student' => null,
            'registrations' => null,
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
            'editable' => true,
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

    private function updateStudentFromRequest(int $sid): Student
    {
        $data = $this->getRequest()->request->all();

        if (!Validator::validateRequired($data, ['firstname', 'lastname', 'email', 'birth_date'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        /** @var Student $student */
        $student = $this->entityManager->getQueryBuilder(Student::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $sid,
            ])
            ->limit(1)
            ->result();

        try {
            $student->setFirstName($data['firstname']);
            $student->setLastName($data['lastname']);
            $student->setEmail($data['email']);
            $student->setBirthDate(new \DateTime($data['birth_date']));

            /** @var ExamRegistration[] $registrations */
            $registrations = $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('student_id = :student_id')
                ->setParameters([
                    'student_id' => $sid
                ])
                ->result();

            if (!is_array($data['exam_ids'])) {
                $data['exam_ids'] = [$data['exam_ids']];
            }

            foreach ($registrations as $registration) {
                if (!in_array($registration->getExam()->getId(), $data['exam_ids'])) {
                    $this->entityManager->remove($registration);
                } else {
                    $i = array_search($registration->getExam()->getId(), $data['exam_ids']);
                    unset($data['exam_ids'][$i]);
                }
            }

            $this->entityManager->flush();

            if (empty($data['exam_ids'])) return $student;

            foreach ($data['exam_ids'] as $exam_id) {
                $exam = $this->entityManager->getQueryBuilder(Exam::class)
                    ->where('id = :id')
                    ->setParameters([
                        'id' => $exam_id
                    ])
                    ->limit(1)
                    ->result();

                $registration = new ExamRegistration($exam, $student, new \DateTime('now'), null, null);
                $this->entityManager->persist($registration);
            }

            $this->entityManager->flush();
        } catch (\Exception) {
            throw new BadRequestException('Something went wrong with the field config');
        }

        return $student;
    }

    private function newStudentFromRequest(): Student
    {
        $data = $this->getRequest()->request->all();

        if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['birth_date'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        try {
            $student = new Student($data['firstname'], $data['lastname'], $data['email'], "defaultpassword", new \DateTime($data['birth_date']));
            $student = $this->entityManager->persist($student);
            $this->entityManager->flush();

            if (!isset($data['exam_id'])) {
                if (!is_array($data['exam_ids'])) {
                    $data['exam_ids'] = [$data['exam_ids']];
                }

                foreach ($data['exam_ids'] as $id) {
                    $exam = $this->entityManager->getQueryBuilder(Exam::class)
                            ->where('id = :id')
                            ->setParameters([
                                'id' => $id
                            ])
                            ->limit(1)
                            ->result();
                    $registration = new ExamRegistration($exam, $student, new \DateTime('now'), null, null);
                    $this->entityManager->persist($registration);
                }

                $this->entityManager->flush();
            }

            return $student;
        } catch (\Exception) {
            // TODO: This could be tricky, as it could also catch other errors, but we lack validation
            //  so this is the way now.
            // TODO: add log
            throw new BadRequestException('Something went wrong with the field config');
        }
    }
}
