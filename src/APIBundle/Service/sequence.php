<?php

namespace EntityBundle\Service;

use APIBundle\Entity\EvaluationSequence;
use Doctrine\ORM\EntityManager;

class sequence
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function create($session)
    {
        $parameters = $this->setParameters($session);

        $sequence = null;
        switch ($parameters) {
            case "singleEvaluation":
                $sequence = $this->instance($session, false, null, "singleEvaluation");
                break;
            case "singleEvaluationContextualized":
                $sequence = $this->instance($session, true, null, "singleEvaluation");
                break;
            case "browseEvaluation":
                $sequence = $this->instance($session, false, null, "browseEvaluation");
                break;
            case "browseEvaluationContextualized":
                $sequence = $this->instance($session, true, null, "browseEvaluation");
                break;
        }

        return $sequence;
    }

    public function setParameters($session)
    {
        /*
         *  WARNING: There is a bias with this function:
         *      It only focuses on the user side (if the user did or didn't do this kind of expe before)
         *      It should also take into account the average of users, in order to have something balanced
         */

        $sequences = $this->getBySession($session);

        $sequencesTypeSingleEvaluation = 0;
        $sequencesTypeSingleEvaluationContextualized = 0;
        $sequencesTypeBrowseEvaluation = 0;
        $sequencesTypeBrowseEvaluationContextualized = 0;

        foreach($sequences as $sequence) {
            if($sequence->getType() == 'singleEvaluation' and $sequence->getContextualized() == false) {
                $sequencesTypeSingleEvaluation++;
            } elseif($sequence->getType() == 'singleEvaluation' and $sequence->getContextualized() == true) {
                $sequencesTypeSingleEvaluationContextualized++;
            } elseif($sequence->getType() == 'browseEvaluation' and $sequence->getContextualized() == false) {
                $sequencesTypeBrowseEvaluation++;
            } elseif($sequence->getType() == 'browseEvaluation' and $sequence->getContextualized() == true) {
                $sequencesTypeBrowseEvaluationContextualized++;
            }
        }

        $array = [
           "singleEvaluation" => $sequencesTypeSingleEvaluation,
           "singleEvaluationContextualized" => $sequencesTypeSingleEvaluationContextualized,
           "browseEvaluation" => $sequencesTypeBrowseEvaluation,
           "browseEvaluationContextualized" => $sequencesTypeBrowseEvaluationContextualized,
        ];

        return array_keys($array, min($array));
    }

    public function instance($session, $contextualized, $referenceItemId, $type)
    {
        $sequence = new EvaluationSequence();
        $sequence->setSession($session);
        $sequence->setContextualized($contextualized);
        $sequence->setReferenceItem($referenceItemId);
        $sequence->setType($type);
        $sequence->setEndDate(new \DateTime());

        $this->em->persist($sequence);
        $this->em->flush();

        return $sequence;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationSequence')->findOneById($id);
    }

    public function getBySession($session)
    {
        return $this->em->getRepository('APIBundle:EvaluationSequence')->findBySession($session);
    }
}
