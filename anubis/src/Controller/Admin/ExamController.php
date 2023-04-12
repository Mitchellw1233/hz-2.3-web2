<?php

namespace App\Controller\Admin;

use Slimfony\HttpFoundation\Response;

class ExamController extends AbstractAdminController
{
    public function list(): Response
    {
        return $this->render('pages/test.php');
    }
}
