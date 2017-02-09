<?php

namespace EntityBundle\Service;

use APIBundle\Entity\EvaluationProposalSingle;
use Doctrine\ORM\EntityManager;

class referenceItem
{
    protected $em;
    protected $buzz;
    protected $sequence;
    protected $proposalBrowse;
    protected $proposalSingle;

    public function __construct(EntityManager $EntityManager, $buzz, sequence $sequence, proposalBrowse $proposalBrowse, proposalSingle $proposalSingle)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->proposalBrowse = $proposalBrowse;
        $this->proposalSingle = $proposalSingle;
    }

    public function getReferenceItem($proposal)
    {
        $session = $proposal->getSequence()->getSession();
        $oldReferenceItems = array();

        foreach($this->sequence->getBySession($session) as $sequence) {
            foreach($this->proposalBrowse->getBySequence($sequence) as $proposalBrowseOccurrence) {

            }
            foreach($this->proposalSingle->getBySequence($sequence) as $proposalSingleOccurrence) {
                $oldReferenceItems[] = $proposalSingleOccurrence->getReferenceItem();
            }
        }

        $newReferenceItemId = $this->getEntityId($oldReferenceItems);

        return $newReferenceItemId;
    }

    public function getEntityId($ids)
    {
        $properties = $this->em->getRepository('EntityBundle:EntityPropoerty')->getUnusedEntity($ids);

        if(count($properties) > 0) {
            shuffle($properties);
            return $properties[0]->getEuropeanaId();
        } else {
            return null;
        }
    }
}
