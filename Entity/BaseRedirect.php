<?php

namespace Rz\RedirectBundle\Entity;

use Rz\RedirectBundle\Model\Redirect;

abstract class BaseRedirect extends Redirect
{
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}