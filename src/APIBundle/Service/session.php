<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationSession;
use Doctrine\ORM\EntityManager;
use EntityBundle\Service\process;

class session
{
    protected $em;
    protected $buzz;
    protected $log;
    protected $process;

    public function __construct(EntityManager $EntityManager, $buzz, log $log, process $process)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->log = $log;
        $this->process = $process;
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
                $session = $this->instance($user, false, $this->setReferenceItem($user), "browseEvaluation");
                break;
            case "browseEvaluationContextualized":
                $session = $this->instance($user, true, $this->setReferenceItem($user), "browseEvaluation");
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

        $this->log->log("------------------------------------------------------------------", 'api');
        $this->log->log("-> api.session->setParameters() -- ".date('Y/m/d h:i:s a', time()), 'api');


        $array = [
            "singleEvaluation" => 0,
            "singleEvaluationContextualized" => 0,
            "browseEvaluation" => 0,
            "browseEvaluationContextualized" => 0,
        ];

        foreach($this->getByUser($user) as $session) {
            if($session->getType() == 'singleEvaluation' and $session->getContextualized() == false) {
                $array['singleEvaluation']++;
            } elseif($session->getType() == 'singleEvaluation' and $session->getContextualized() == true) {
                $array['singleEvaluationContextualized']++;
            } elseif($session->getType() == 'browseEvaluation' and $session->getContextualized() == false) {
                $array['browseEvaluation']++;
            } elseif($session->getType() == 'browseEvaluation' and $session->getContextualized() == true) {
                $array['browseEvaluationContextualized']++;
            }
        }

        if($this->setReferenceItem($user) == null) {
            $array['browseEvaluation'] += 1000;
            $array['browseEvaluationContextualized'] += 1000;
        }

        asort($array);
        reset($array);

        $this->log->log(json_encode($array), 'api');
        $this->log->log("Returned type: ".key($array), 'api');

        return key($array);
        //return "singleEvaluation";
    }

    public function instance($user, $contextualized, $referenceItemId, $type)
    {
        $session = new EvaluationSession();
        $session->setUser($user);
        $session->setContextualized($contextualized);
        $session->setReferenceItem($referenceItemId);
        $session->setType($type);
        $session->setEndDate(new \DateTime());
        $session->setEndSession(false);

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
        foreach($this->em->getRepository('APIBundle:EvaluationProposalBrowse')->findBy(array('session' => $session)) as $proposal) {
            $array[] = ['proposal_type' => 'browseEvaluation', 'date' => $proposal->getCreateDate(), 'proposal' => $proposal];
        }

        return $array;
    }

    public function setReferenceItem($user)
    {
        $referenceItems = $this->defineListReferenceItems();
        $sessionsWithReferenceItem = array();
        foreach($this->em->getRepository('APIBundle:EvaluationSession')->getReferenceItems($user) as $session) {
            $sessionsWithReferenceItem[] = $session->getReferenceItem();
        }

        $selectedReferenceItems =
            array_diff(
                $referenceItems,
                $sessionsWithReferenceItem);

        if(count($selectedReferenceItems) > 0) {
            shuffle($selectedReferenceItems);
            return $selectedReferenceItems[0];
        } else {
            return null;
        }

    }

    public function defineListReferenceItems()
    {
        $entitiesFetch = $this->em->getRepository('EntityBundle:EntityFetch')->findBy(array('processed' => true));

        $referenceItems = array();
        foreach($entitiesFetch as $entityFetch) {
            $referenceItems[] = $this->process->getEuropeanaId($entityFetch->getUri());
        }

        return $referenceItems;
    }
}
