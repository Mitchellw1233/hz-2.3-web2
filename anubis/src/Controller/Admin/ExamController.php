<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\Teacher;
use App\Util\Validator;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\BadRequestException;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;

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

    public function list(): Response
    {
        if (!$this->verify()) {
            throw new ForbiddenException();
        }

        return $this->render('pages/admin/exam/list.php', [
            'exams' => $this->entityManager->getQueryBuilder(Exam::class)->result(),
        ]);
    }

    public function single(int $id): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $exam = $this->entityManager->persist($this->updateExamFromRequest());
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
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $exam = $this->entityManager->persist($this->newExamFromRequest());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.exam.single', ['id' => $exam->getId()]);
        }

        return $this->render('pages/admin/exam/crud.php', [
            'exam' => null,
            'teachers' => $this->entityManager->getQueryBuilder(Teacher::class)->result(),
            'editable' => true,
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

    private function updateExamFromRequest(): Exam
    {
        $data = $this->getRequest()->request->all();

        if (!Validator::validateRequired($data, ['id', 'name', 'teacher_id', 'exam_date', 'credits'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

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
                'id' => $data['id'],
            ])
            ->limit(1)
            ->result();
        try {
            $exam->setName($data['name']);
            $exam->setTeacher($teacher);
            $exam->setExamDate(new \DateTime($data['exam_date']));
            $exam->setCredits($data['credits']);
        } catch (\Exception) {
            throw new BadRequestException('Something went wrong with the field config');
        }

        return $exam;
    }

    private function newExamFromRequest(): Exam
    {
        $data = $this->getRequest()->request->all();
        if (!isset($data['name'], $data['teacher_id'], $data['exam_date'], $data['credits'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['teacher_id'],
            ])
            ->limit(1)
            ->result();
        try {
            return new Exam($data['name'], $teacher, new \DateTime($data['exam_date']), $data['credits']);
        } catch (\Exception) {
            // TODO: This could be tricky, as it could also catch other errors, but we lack validation
            //  so this is the way now.
            // TODO: add log
            throw new BadRequestException('Something went wrong with the field config');
        }
    }
}
