<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationProposalBrowse;
use Doctrine\ORM\EntityManager;
use EntityBundle\Service\graph;

class context
{
    protected $em;
    protected $buzz;
    protected $log;
    protected $graph;
    protected $proposalSingle;
    protected $proposalBrowse;
    protected $proposalBrowseItem;

    public function __construct(EntityManager $EntityManager, $buzz, log $log, graph $graph, proposalSingle $proposalSingle, proposalBrowse $proposalBrowse, proposalBrowseItem $proposalBrowseItem)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->log = $log;
        $this->graph = $graph;
        $this->proposalSingle = $proposalSingle;
        $this->proposalBrowse = $proposalBrowse;
        $this->proposalBrowseItem = $proposalBrowseItem;
    }

    public function getForProposalSingle($proposalSingle)
    {
        return $this->getContext($proposalSingle->getReferenceItem(), $proposalSingle->getSuggestedItem(), true);
    }

    public function getForAdmin($referenceItemId, $suggestedItemId)
    {
        return $this->getContext($referenceItemId, $suggestedItemId, false);
    }

    public function getContext($referenceId, $suggestionId, $endUserable)
    {
        $compareArray = $this->compare($referenceId, $suggestionId);
        $render = array();

        $referenceItem = (array) $this->graph->buildObject($referenceId);

        if($compareArray['dcCreator'] == true AND $referenceItem['dcCreator'] != null) {
            $render[] = ($endUserable == true) ? 'creator' : 'dcCreator';
        } if($compareArray['dcContributor'] == true AND $referenceItem['dcContributor'] != null) {
            $render[] = ($endUserable == true) ? 'contributor' : 'dcContributor';
        } if($compareArray['edmDatasetName'] == true AND $referenceItem['edmDatasetName'] != null) {
            $render[] = ($endUserable == true) ? 'dataset' : 'edmDatasetName';
        } if($compareArray['language'] == true AND $referenceItem['language'] != null) {
            $render[] = ($endUserable == true) ? 'language' : 'language';
        } if($compareArray['edmDataProvider'] == true AND $referenceItem['edmDataProvider'] != null) {
            $render[] = ($endUserable == true) ? 'data provider' : 'edmDataProvider';
        } if($compareArray['edmProvider'] == true AND $referenceItem['edmProvider'] != null) {
            $render[] = ($endUserable == true) ? 'provider' : 'edmProvider';
        } if($compareArray['dcFormat'] == true AND $referenceItem['dcFormat'] != null) {
            $render[] = ($endUserable == true) ? 'format' : 'dcFormat';
        } if($compareArray['dcLanguage'] == true AND $referenceItem['dcLanguage'] != null) {
            $render[] = ($endUserable == true) ? 'language' : 'dcLanguage';
        } if($compareArray['dcPublisher'] == true AND $referenceItem['dcPublisher'] != null) {
            $render[] = ($endUserable == true) ? 'publisher' : 'dcPublisher';
        } if($compareArray['dcSubject'] == true AND $referenceItem['dcSubject'] != null) {
            $render[] = ($endUserable == true) ? 'subject' : 'dcSubject';
        } if($compareArray['dcTitle'] == true AND $referenceItem['dcTitle'] != null) {
            $render[] = ($endUserable == true) ? 'title' : 'dcTitle';
        } if($compareArray['dcType'] == true AND $referenceItem['dcType'] != null) {
            $render[] = ($endUserable == true) ? 'type' : 'dcType';
        } if($compareArray['dctermsMedium'] == true AND $referenceItem['dctermsMedium'] != null) {
            $render[] = ($endUserable == true) ? 'medium' : 'dctermsMedium';
        } if($compareArray['dctermsTemporal'] == true AND $referenceItem['dctermsTemporal'] != null) {
            $render[] = ($endUserable == true) ? 'date' : 'dctermsTemporal';
        } if($compareArray['dctermsProvenance'] == true AND $referenceItem['dctermsProvenance'] != null) {
            $render[] = ($endUserable == true) ? 'provenance' : 'dctermsProvenance';
        } if($compareArray['dctermsSpatial'] == true AND $referenceItem['dctermsSpatial'] != null) {
            $render[] = ($endUserable == true) ? 'spatial' : 'dctermsSpatial';
        } if($compareArray['edmType'] == true AND $referenceItem['edmType'] != null) {
            $render[] = ($endUserable == true) ? 'type' : 'edmType';
        }

        return $render;
    }

    public function compare($itemAId, $itemBId)
    {
        $itemA = (array) $this->graph->buildObject($itemAId);
        $itemB = (array) $this->graph->buildObject($itemBId);
        $compare = array();

        foreach($itemA as $property => $value) {
            $compare[$property] = ($itemB[$property] == $value) ? true : false;
        }

        return $compare;
    }
}
