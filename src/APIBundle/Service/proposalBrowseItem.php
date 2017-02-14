<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationProposalBrowseItem;
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

    public function create($proposal, $referenceItem, $lastReferenceItems)
    {
        $listItems = array();
        $listItemsIds = array();
        $listItemsAlgo = array();

        if($referenceItem != null) {
            foreach ($this->em->getRepository('EntityBundle:EntityRelation')->findBy(array('entity1' => $referenceItem)) as $entityRelation) {
                if(     $referenceItem != $entityRelation->getEntity2()
                    AND !in_array($entityRelation->getEntity2(), $lastReferenceItems)
                    AND !in_array($entityRelation->getEntity2(), $listItemsIds)
                    AND !in_array($entityRelation->getAlgorithm(), $listItemsAlgo)) {

                    $proposalBrowseItem = new EvaluationProposalBrowseItem();
                    $proposalBrowseItem->setItem($entityRelation->getEntity2());
                    $proposalBrowseItem->setProposal($proposal);
                    $proposalBrowseItem->setAlgorithm($entityRelation->getAlgorithm());
                    $this->em->persist($proposalBrowseItem);

                    $listItems[] = $proposalBrowseItem;
                    $listItemsIds[] = $entityRelation->getEntity2();
                    $listItemsAlgo[] = $entityRelation->getAlgorithm();
                }
            }
            if(count($listItems) == 0) $listItems = null;
        } else {$listItems = null;}

        return $listItems;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowseItem')->findOneById($id);
    }

    public function getByProposalBrowse($proposalBrowse)
    {
        return $this->em->getRepository('APIBundle:EvaluationProposalBrowseItem')->findBy(array('proposal' => $proposalBrowse));
    }

    public function remove($proposalBrowseItem)
    {
        foreach($this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBy(array('choicedItem' => $proposalBrowseItem)) as $proposalBrowse)
        {
            $proposalBrowse->setChoicedItem(null);
        }

        $this->em->remove($proposalBrowseItem);
        $this->em->flush();
    }
}
