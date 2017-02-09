<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;

class proposalBrowseItems
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }
}
