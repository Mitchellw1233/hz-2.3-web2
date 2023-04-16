<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Interface\UserInterface;
use App\Entity\Student;
use App\Entity\Teacher;
use Slimfony\Routing\AbstractController as AbstractSlimfonyController;

class AbstractBaseController extends AbstractSlimfonyController
{
    public function setUser(?UserInterface $user): void
    {
        if ($user === null) {
            $this->getRequest()->getSession()->remove('_user');
            return;
        }

        $this->getRequest()->getSession()->set('_user', serialize($user));
    }

    public function getUser(): ?UserInterface
    {
        if (!$this->getRequest()->getSession()->has('_user')) {
            return null;
        }

        return unserialize($this->getRequest()->getSession()->get('_user'));
    }
}
