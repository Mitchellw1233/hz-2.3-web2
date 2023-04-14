<?php

namespace App\Controller;

use App\Entity\Student;
use Slimfony\Routing\AbstractController as AbstractSlimfonyController;

class AbstractBaseController extends AbstractSlimfonyController
{
    protected function verify(): bool
    {
        return $this->getUser() instanceof Student;
    }
}
