<?php

namespace APIBundle\Service;

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
        $referenceItem  = ($this->getLastProposalBySession($session) != null AND $this->getLastProposalBySession($session)->getChoicedItem() != null)
                        ? $this->getLastProposalBySession($session)->getChoicedItem()->getItem()
                        : $session->getReferenceItem();

        $forceBreak = ($this->getLastProposalBySession($session) != null) ? $this->getLastProposalBySession($session)->getChoiceNull() : false ;

        $proposal = new EvaluationProposalBrowse();
        $proposal->setSession($session);
        $proposal->setReferenceItem($referenceItem);
        $proposal->setChoicedItem(null);
        $proposal->setChoiceNull(false);
        $this->em->persist($proposal);
        $proposalBrowseItems = $this->proposalBrowseItem->create($proposal, $referenceItem, $this->getReferenceItems($session));

        if($proposalBrowseItems != null AND $referenceItem != null AND $forceBreak == false) {
            $this->em->flush();
            return $proposal;
        } else {
            $this->em->clear();
            return null;
        }
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findOneById($id);
    }

    public function getBySession($session)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBySession($session);
    }

    public function getLastProposalBySession($session)
    {
        $proposals = $this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBy(array('session' => $session), array('createDate' => 'DESC'));
        return (count($proposals) > 0) ? $proposals[0] : null;
    }

    public function remove($proposal)
    {
        foreach($this->proposalBrowseItem->getByProposalBrowse($proposal) as $proposalBrowseItem) {
            $this->proposalBrowseItem->remove($proposalBrowseItem);
        }

        $this->em->remove($proposal);
        $this->em->flush();
    }

    public function getReferenceItems($session)
    {
        $referenceItems = [];
        foreach($this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBy(array('session' => $session)) as $proposalBrowse) {
            $referenceItems[] = $proposalBrowse->getReferenceItem();
        }

        return $referenceItems;
    }
}
