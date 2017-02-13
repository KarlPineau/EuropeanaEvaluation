<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationSession;
use Doctrine\ORM\EntityManager;

class session
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function create($user)
    {
        $parameters = $this->setParameters($user);

        $session = null;
        switch ($parameters) {
            case "singleEvaluation":
                $session = $this->instance($user, false, null, "singleEvaluation");
                break;
            case "singleEvaluationContextualized":
                $session = $this->instance($user, true, null, "singleEvaluation");
                break;
            case "browseEvaluation":
                $session = $this->instance($user, false, null, "browseEvaluation");
                break;
            case "browseEvaluationContextualized":
                $session = $this->instance($user, true, null, "browseEvaluation");
                break;
        }

        return $session;
    }

    public function setParameters($user)
    {
        /*
         *  WARNING: There is a bias with this function:
         *      It only focuses on the user side (if the user did or didn't do this kind of expe before)
         *      It should also take into account the average of users, in order to have something balanced
         */

        $sessions = $this->getByUser($user);

        $sessionsTypeSingleEvaluation = 0;
        $sessionsTypeSingleEvaluationContextualized = 0;
        $sessionsTypeBrowseEvaluation = 0;
        $sessionsTypeBrowseEvaluationContextualized = 0;

        foreach($sessions as $session) {
            if($session->getType() == 'singleEvaluation' and $session->getContextualized() == false) {
                $sessionsTypeSingleEvaluation++;
            } elseif($session->getType() == 'singleEvaluation' and $session->getContextualized() == true) {
                $sessionsTypeSingleEvaluationContextualized++;
            } elseif($session->getType() == 'browseEvaluation' and $session->getContextualized() == false) {
                $sessionsTypeBrowseEvaluation++;
            } elseif($session->getType() == 'browseEvaluation' and $session->getContextualized() == true) {
                $sessionsTypeBrowseEvaluationContextualized++;
            }
        }

        $array = [
           "singleEvaluation" => $sessionsTypeSingleEvaluation,
           "singleEvaluationContextualized" => $sessionsTypeSingleEvaluationContextualized,
           "browseEvaluation" => $sessionsTypeBrowseEvaluation,
           "browseEvaluationContextualized" => $sessionsTypeBrowseEvaluationContextualized,
        ];

        //return array_keys($array, min($array));
        return "singleEvaluation";
    }

    public function instance($user, $contextualized, $referenceItemId, $type)
    {
        $session = new EvaluationSession();
        $session->setUser($user);
        $session->setContextualized($contextualized);
        $session->setReferenceItem($referenceItemId);
        $session->setType($type);
        $session->setEndDate(new \DateTime());

        $this->em->persist($session);
        $this->em->flush();

        return $session;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationSession')->findOneById($id);
    }

    public function getByUser($user)
    {
        return $this->em->getRepository('APIBundle:EvaluationSession')->findByUser($user);
    }

    public function getProposals($session)
    {
        $array = array();

        foreach($this->em->getRepository('APIBundle:EvaluationProposalSingle')->findBy(array('session' => $session)) as $proposal) {
            $array[] = ['proposal_type' => 'singleEvaluation', 'date' => $proposal->getCreateDate(), 'proposal' => $proposal];
        }

        return $array;
    }
}
