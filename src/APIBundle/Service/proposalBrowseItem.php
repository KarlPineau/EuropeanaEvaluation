<?php

namespace APIBundle\Service;

use Doctrine\ORM\EntityManager;

class proposalBrowseItem
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function getByProposalBrowse($proposalBrowse)
    {
        return $this->em->getRepository('APIBundle:ProposalBrowseItem')->findBy(array('proposal' => $proposalBrowse));
    }

    public function remove($proposalBrowseItem)
    {
        $this->em->remove($proposalBrowseItem);
        $this->em->flush();
    }
}
