<?php

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Admin;

abstract class AbstractAdminController extends AbstractBaseController
{
    protected function verify(): bool
    {
        return true;  // TODO: REMOVE!
        return $this->getUser() instanceof Admin || parent::verify();
    }
}
