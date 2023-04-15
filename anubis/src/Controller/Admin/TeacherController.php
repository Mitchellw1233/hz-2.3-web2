<?php

namespace App\Controller\Admin;

use App\Entity\Teacher;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\RedirectResponse;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;
use Slimfony\Routing\RouteResolver;
use Slimfony\Validation\Constraint;
use Slimfony\Validation\Exception\ValidationException;
use Slimfony\Validation\Validator;

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

    public static function validationSchema(): array
    {
        return [
            'firstname' => new Constraint('string'),
            'lastname' => new Constraint('string'),
            'email' => new Constraint('string'),
            'birth_date' => new Constraint('string'),
        ];
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
            try {
                $teacher = $this->entityManager->persist($this->updateTeacherFromRequest($id));
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.teacher.single', ['id' => $id],
                    ['errors' => $e->getMessage(), 'edit' => 'true']);
            }
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
            'errors' => $this->getRequest()->getQuery()->get('errors'),
        ]);
    }

    public function create(): Response
    {
        if (strtoupper($this->getRequest()->getMethod()) === 'POST') {
            try {
                $teacher = $this->entityManager->persist($this->newTeacherFromRequest());
            } catch (ValidationException $e) {
                return $this->redirectToRoute('admin.teacher.create', [], ['errors' => $e->getMessage()]);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.teacher.single', ['id' => $teacher->getId()]);
        }

        return $this->render('pages/admin/teacher/crud.php', [
            'teacher' => null,
            'editable' => true,
            'errors' => $this->getRequest()->getQuery()->get('errors'),
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

    /**
     * @return Teacher
     *
     * @throws ValidationException
     */
    private function updateTeacherFromRequest(int $id): Teacher
    {
        $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());

        /** @var Teacher $teacher */
        $teacher = $this->entityManager->getQueryBuilder(Teacher::class)
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->limit(1)
            ->result();

        $teacher->setFirstName($data['firstname']);
        $teacher->setLastName($data['lastname']);
        $teacher->setEmail($data['email']);
        $teacher->setBirthDate(new \DateTime($data['birth_date']));

        return $teacher;
    }

    /**
     * @return Teacher
     *
     * @throws ValidationException
     */
    private function newTeacherFromRequest(): Teacher
    {
        $data = Validator::validate($this->getRequest()->request->all(), self::validationSchema());

        return new Teacher($data['firstname'], $data['lastname'], $data['email'],
            'defaultpassword', new \DateTime($data['birth_date']));
    }
}
