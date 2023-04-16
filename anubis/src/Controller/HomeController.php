<?php

namespace App\Controller;

use Slimfony\HttpFoundation\Response;

class HomeController extends AbstractBaseController
{
    public function home(): Response
    {
        return $this->render('pages/home.php');
    }
}