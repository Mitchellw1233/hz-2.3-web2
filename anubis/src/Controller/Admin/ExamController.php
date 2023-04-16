<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\ExamRegistration;
use App\Entity\Teacher;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\ORM\EntityManager;
use Slimfony\ORM\Query\OrderByEnum;
use Slimfony\Routing\RouteResolver;
use Slimfony\Validation\Constraint;
use Slimfony\Validation\Exception\ValidationException;
use Slimfony\Validation\Validator;

class ExamController extends AbstractAdminController
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
            'name' => new Constraint('string'),
            'teacher_id' => new Constraint('integer', empty: true),
            'exam_date' => new Constraint('string'),
            'credits' => new Constraint('integer'),
            'student_grades' => new Constraint('float', nullable: true, empty: true, onEmptyReturn: true)
        ];
    }

    public function list(): Response
    {
        $isTeacher = false;
        $user = $this->getUser();
        $query = $this->entityManager->getQueryBuilder(Exam::class);

        // If teacher, only exams controlled by teacher
        if ($user instanceof Teacher) {
            $isTeacher = true;
            $query->where('teacher_id = :id')
                ->setParameters([
                    'id' => $user->getId(),
                ]);
        }

        return $this->render('pages/admin/exam/list.php', [
            'exams' => $query->result(),
            'isTeacher' => $isTeacher,
        ]);
    }

    public function single(int $id): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.exam.single', ['id' => $id], ['edit' => 'true', 'errors' => $e->getMessage()]);
            }
            $exam = $this->entityManager->persist($this->updateExamFromRequest($id, $data));
            $this->persistGradesFromRequest($id, $data);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.exam.single', ['id' => $exam->getId()]);
        }

        return $this->render('pages/admin/exam/crud.php', [
            'exam' => $this->entityManager->getQueryBuilder(Exam::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result(),
            'registrations' => $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('exam_id = :id')->setParameters(['id' => $id])
                ->orderBy(OrderByEnum::ASC, 'student_id')
                ->result(),
            'teachers' => $this->entityManager->getQueryBuilder(Teacher::class)->result(),
            'editable' => $this->getRequest()->getQuery()->get('edit') === 'true',
            'errors' => $this->getRequest()->query->get('errors'),
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.exam.create', [], ['errors' => $e->getMessage()]);
            }
            $exam = $this->entityManager->persist($this->newExamFromRequest($data));
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.exam.single', ['id' => $exam->getId()]);
        }

        return $this->render('pages/admin/exam/crud.php', [
            'exam' => null,
            'teachers' => $this->entityManager->getQueryBuilder(Teacher::class)->result(),
            'editable' => true,
            'errors' => $this->getRequest()->query->get('errors'),
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        $this->entityManager->remove(
            $this->entityManager->getQueryBuilder(Exam::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result()
        );
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.exam.list');
    }

    private function updateExamFromRequest(int $id, array $data): Exam
    {
        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['teacher_id'],
            ])
            ->limit(1)
            ->result();

        /** @var Exam $exam */
        $exam = $this->entityManager->getQueryBuilder(Exam::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->limit(1)
            ->result();

        $exam->setName($data['name']);
        $exam->setTeacher($teacher);
        $exam->setExamDate(new \DateTime($data['exam_date']));
        $exam->setCredits($data['credits']);

        return $exam;
    }

    private function newExamFromRequest(array $data): Exam
    {
        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['teacher_id'],
            ])
            ->limit(1)
            ->result();

        return new Exam($data['name'], $teacher, new \DateTime($data['exam_date']), $data['credits']);
    }

    /**
     * @param array<string, float> $data
     *
     * @return ExamRegistration[]
     */
    private function persistGradesFromRequest(int $id, array $data): array
    {
        $registrations = [];
        foreach ($data['student_grades'] ?? [] as $sId => $grade) {
            if ($grade === '') {
                $grade = null;
            }

            /** @var ExamRegistration $registration */
            $registration = $this->entityManager->getQueryBuilder(ExamRegistration::class)
                ->where('exam_id = :exam_id AND student_id = :student_id')
                ->setParameters(['exam_id' => $id, 'student_id' => $sId])
                ->limit(1)
                ->result();
            $registration->setGrade($grade);
            $registration->setGradedAt(new \DateTime());

            $registrations[] = $this->entityManager->persist($registration);
        }

        return $registrations;
    }
}
