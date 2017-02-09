<?php

namespace EntityBundle\Service;

use APIBundle\Entity\EvaluationProposalSingle;
use Doctrine\ORM\EntityManager;

class proposalSingle
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
        $proposal = new EvaluationProposalSingle();
        $proposal->setSequence($sequence);
        $proposal->setReferenceItem(null);
        $proposal->setSuggestedItem(null);

        $this->em->persist($proposal);
        $this->em->flush();

        return $proposal;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalSingle')->findOneById($id);
    }

    public function getBySequence($sequence)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalSingle')->findBySequence($sequence);
    }
}
