<?php

namespace EntityBundle\Service;

use APIBundle\Entity\EvaluationProposalSingle;
use Doctrine\ORM\EntityManager;

class proposalSingle
{
    protected $em;
    protected $buzz;
    protected $sequence;

    public function __construct(EntityManager $EntityManager, $buzz, sequence $sequence)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->sequence = $sequence;
    }

    public function create($sequence)
    {
        $proposal = new EvaluationProposalSingle();
        $proposal->setSequence($sequence);

        $entityRelation = $this->getEntityRelation($proposal);
        if($entityRelation != null) {
            $proposal->setReferenceItem($entityRelation->getEntity1());
            $proposal->setSuggestedItem($entityRelation->getEntity2());

            $this->em->persist($proposal);
            $this->em->flush();

            return $proposal;
        } else {
            return null;
        }
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalSingle')->findOneById($id);
    }

    public function getBySequence($sequence)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalSingle')->findBySequence($sequence);
    }

    public function getEntityRelation($proposal)
    {
        $entityRelations = $this->em->getRepository('EntityBundle:EntityRelation')->findAll();
        if(count($entityRelations) > 0) {
            shuffle($entityRelations);
            $selectedEntityRelation = $entityRelations[0];

            if($this->checkEntityRelation($selectedEntityRelation, $proposal) == true) {
                return $selectedEntityRelation;
            } else {
                return $this->getEntityRelation($proposal);
            }
        } else {
            return null;
        }
    }

    public function checkEntityRelation($entityRelation, $proposal)
    {
        foreach ($this->sequence->getBySession($proposal->getSequence()->getSession()) as $sequence) {
            foreach ($this->getBySequence($sequence) as $proposalSingleOccurrence) {
                $item1 = $proposalSingleOccurrence->getReferenceItem();
                $item2 = $proposalSingleOccurrence->getSuggestedItem();
                $entityRelationItem1 = $entityRelation->getItem1();
                $entityRelationItem2 = $entityRelation->getItem2();

                if(($item1 == $entityRelationItem1 AND $item2 == $entityRelationItem2) OR ($item2 == $entityRelationItem1 AND $item1 == $entityRelationItem2)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
}
