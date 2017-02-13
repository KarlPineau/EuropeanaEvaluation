<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationProposal;
use APIBundle\Entity\EvaluationProposalBrowse;
use Doctrine\ORM\EntityManager;

class proposalBrowse
{
    protected $em;
    protected $buzz;
    protected $proposalBrowseItem;

    public function __construct(EntityManager $EntityManager, $buzz, proposalBrowseItem $proposalBrowseItem)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->proposalBrowseItem = $proposalBrowseItem;
    }

    public function create($session)
    {
        $proposal = new EvaluationProposalBrowse();
        $proposal->setSession($session);
        $proposal->setChoicedItem(null);
        $this->em->persist($proposal);
        $this->em->flush();

        return $proposal;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findOneById($id);
    }

    public function getBySession($session)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBySession($session);
    }

    public function remove($proposal)
    {
        foreach($this->proposalBrowseItem->getByProposalBrowse() as $proposalBrowseItem) {
            $this->proposalBrowseItem->remove($proposalBrowseItem);
        }

        $this->em->remove($proposal);
        $this->em->flush();
    }
}
