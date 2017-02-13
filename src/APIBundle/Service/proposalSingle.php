<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationProposalSingle;
use Doctrine\ORM\EntityManager;

class proposalSingle
{
    protected $em;
    protected $buzz;
    protected $session;

    public function __construct(EntityManager $EntityManager, $buzz, session $session)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->session = $session;
    }

    public function create($session)
    {
        $proposal = new EvaluationProposalSingle();
        $proposal->setSession($session);

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

    public function getBySession($session)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalSingle')->findBySession($session);
    }

    public function remove($proposal)
    {
        $this->em->remove($proposal);
        $this->em->flush();
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
        $return = true;

        foreach ($this->session->getByUser($proposal->getSession()->getUser()) as $session) {
            foreach ($this->getBySession($session) as $proposalSingleOccurrence) {
                $item1 = $proposalSingleOccurrence->getReferenceItem();
                $item2 = $proposalSingleOccurrence->getSuggestedItem();
                $entityRelationItem1 = $entityRelation->getEntity1();
                $entityRelationItem2 = $entityRelation->getEntity2();

                if(($item1 == $entityRelationItem1 AND $item2 == $entityRelationItem2) OR ($item2 == $entityRelationItem1 AND $item1 == $entityRelationItem2)) {
                    $return = false;
                } else {
                    $return = true;
                }
            }
        }

        return $return;
    }
}
