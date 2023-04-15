<?php

namespace App\Controller\Student;

use App\Controller\AbstractBaseController;
use App\Entity\Student;

abstract class AbstractStudentController extends AbstractBaseController
{
    protected function verify(): bool
    {
        return true;  // TODO: REMOVE!
        return $this->getUser() instanceof Student || parent::verify();
    }
}
