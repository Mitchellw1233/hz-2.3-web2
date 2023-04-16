<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\Teacher;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\ORM\EntityManager;
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
                $exam = $this->entityManager->persist($this->updateExamFromRequest($id));
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.exam.single', ['id' => $id], ['edit' => 'true', 'errors' => $e->getMessage()]);
            }
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
            'teachers' => $this->entityManager->getQueryBuilder(Teacher::class)->result(),
            'editable' => $this->getRequest()->getQuery()->get('edit') === 'true',
            'errors' => $this->getRequest()->query->get('errors'),
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $exam = $this->entityManager->persist($this->newExamFromRequest());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.exam.create', [], ['errors' => $e->getMessage()]);
            }
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

    /**
     * @param int $id
     * @return Exam
     * @throws ValidationException
     */
    private function updateExamFromRequest(int $id): Exam
    {
        $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());

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

    /**
     * @return Exam
     *
     * @throws ValidationException
     */
    private function newExamFromRequest(): Exam
    {
        $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());

        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['teacher_id'],
            ])
            ->limit(1)
            ->result();

        return new Exam($data['name'], $teacher, new \DateTime($data['exam_date']), $data['credits']);
    }
}
