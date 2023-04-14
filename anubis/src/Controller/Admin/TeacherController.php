<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\Teacher;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\BadRequestException;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;

class TeacherController extends AbstractAdminController
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

        return $this->render('pages/admin/teacher/list.php', [
            'teachers' => $this->entityManager->getQueryBuilder(Teacher::class)->result(),
        ]);
    }

    public function single(int $id): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $teacher = $this->entityManager->persist($this->updateTeacherFromRequest());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.teacher.single', ['id' => $teacher->getId()]);
        }

        return $this->render('pages/admin/teacher/crud.php', [
            'teacher' => $this->entityManager->getQueryBuilder(Teacher::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result(),
            'editable' => $this->getRequest()->getQuery()->get('edit') === 'true',
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $teacher = $this->entityManager->persist($this->newTeacherFromRequest());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.teacher.single', ['id' => $teacher->getId()]);
        }

        return $this->render('pages/admin/teacher/crud.php', [
            'teacher' => null,
            'editable' => true,
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        $this->entityManager->remove(
            $this->entityManager->getQueryBuilder(Teacher::class)
                ->where('id = :id')
                ->setParameters([
                    'id' => $id,
                ])
                ->limit(1)
                ->result()
        );
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.teacher.list');
    }

    private function updateTeacherFromRequest(): Teacher
    {
        $data = $this->getRequest()->request->all();
        if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['birth_date'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        /** @var Teacher $teacher */
        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['id'],
            ])
            ->limit(1)
            ->result();
        try {
            $teacher->setFirstName($data['firstname']);
            $teacher->setLastName($data['lastname']);
            $teacher->setEmail($data['email']);
            $teacher->setBirthDate(new \DateTime($data['birth_date']));
        } catch (\Exception) {
            throw new BadRequestException('Something went wrong with the field config');
        }

        return $teacher;
    }

    private function newTeacherFromRequest(): Teacher
    {
        $data = $this->getRequest()->request->all();
        if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['birth_date'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        try {
            return new Teacher($data['firstname'], $data['lastname'], $data['email'], new \DateTime($data['birth_date']));
        } catch (\Exception) {
            // TODO: This could be tricky, as it could also catch other errors, but we lack validation
            //  so this is the way now.
            // TODO: add log
            throw new BadRequestException('Something went wrong with the field config');
        }
    }
}
