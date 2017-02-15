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
        if($entityRelation != null AND $entityRelation->getEntity1() != null AND $entityRelation->getEntity2() != null AND $entityRelation->getAlgorithm()) {
            $proposal->setReferenceItem($entityRelation->getEntity1());
            $proposal->setSuggestedItem($entityRelation->getEntity2());
            $proposal->setAlgorithm($entityRelation->getAlgorithm());

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
        $entityRelationsId = [];
        foreach($this->em->getRepository('EntityBundle:EntityRelation')->findAll() as $entityRelation) {
            $entityRelationsId[] = $entityRelation->getId();
        }
        $usedEntityRelationsId = $this->getOldEntityRelations($proposal->getSession()->getUser());
        $unusedEntityRelations = array_diff($entityRelationsId, $usedEntityRelationsId);
        if(count($unusedEntityRelations) > 0) {
            shuffle($unusedEntityRelations);
            $id = $unusedEntityRelations[0];
            return $this->em->getRepository('EntityBundle:EntityRelation')->findOneById($id);
        } else {
            return null;
        }
    }

    public function getOldEntityRelations($user)
    {
        $oldEntityRelations = [];
        foreach ($this->session->getByUser($user) as $session) {
            foreach($this->getBySession($session) as $proposalSingleOccurrence) {
                $res = $this->em->getRepository('EntityBundle:EntityRelation')->findOneBy(array('entity1' => $proposalSingleOccurrence->getReferenceItem(), 'entity2' => $proposalSingleOccurrence->getSuggestedItem()));
                if($res != null) {
                    $oldEntityRelations[] = $res->getId();
                }
            }
        }

        return $oldEntityRelations;
    }
}
