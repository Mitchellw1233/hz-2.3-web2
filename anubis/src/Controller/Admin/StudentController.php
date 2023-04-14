<?php

namespace App\Controller\Admin;

use App\Entity\Student;
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
            $student = $this->entityManager->persist($this->updateStudentFromRequest());
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
            'editable' => $this->getRequest()->getQuery()->get('edit') === 'true',
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            $student = $this->entityManager->persist($this->newStudentFromRequest());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.student.single', ['id' => $student->getId()]);
        }

        return $this->render('pages/admin/student/crud.php', [
            'student' => null,
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

    private function updateStudentFromRequest(): Student
    {
        $data = $this->getRequest()->request->all();
        if (!isset($data['firstname'], $data['lastname'], $data['email'], $data['birth_date'])) {
            throw new BadRequestException('Not all fields were filled in');
        }

        /** @var Student $student */
        $student = $this->entityManager->getQueryBuilder(Student::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $data['id'],
            ])
            ->limit(1)
            ->result();
        try {
            $student->setFirstName($data['firstname']);
            $student->setLastName($data['lastname']);
            $student->setEmail($data['email']);
            $student->setBirthDate(new \DateTime($data['birth_date']));
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
            return new Student($data['firstname'], $data['lastname'], $data['email'], new \DateTime($data['birth_date']));
        } catch (\Exception) {
            // TODO: This could be tricky, as it could also catch other errors, but we lack validation
            //  so this is the way now.
            // TODO: add log
            throw new BadRequestException('Something went wrong with the field config');
        }
    }
}
