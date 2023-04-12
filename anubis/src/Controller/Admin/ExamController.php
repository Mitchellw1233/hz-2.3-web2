<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use Slimfony\DependencyInjection\Container;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Exception\ForbiddenException;
use Slimfony\ORM\EntityManager;

class ExamController extends AbstractAdminController
{
    public function __construct(
        Container $container,
        protected EntityManager $entityManager
    )
    {
        parent::__construct($container);
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
}
