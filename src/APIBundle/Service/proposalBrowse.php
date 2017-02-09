<?php

namespace EntityBundle\Service;

use APIBundle\Entity\EvaluationProposal;
use APIBundle\Entity\EvaluationProposalBrowse;
use Doctrine\ORM\EntityManager;

class proposalBrowse
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function create($sequence)
    {
        $proposal = new EvaluationProposalBrowse();
        $proposal->setSequence($sequence);
        $proposal->setChoicedItem(null);
        $this->em->persist($proposal);
        $this->em->flush();

        return $proposal;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findOneById($id);
    }

    public function getBySequence($sequence)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBySequence($sequence);
    }
}
