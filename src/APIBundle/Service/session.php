<?php

namespace EntityBundle\Service;

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

    public function create($user_id=null, $user_locale=null, $user_ref=null, $user_timezone=null)
    {
        $session = new EvaluationSession();
        $session->setContext('facebook_messenger');

        $session->setCreateUserFacebookId($user_id);
        $session->setCreateUserFacebookLocal($user_locale);
        $session->setCreateUserFacebookRef($user_ref);
        $session->setCreateUserFacebookTimezone($user_timezone);

        $this->em->persist($session);
        $this->em->flush();

        return $session;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationSession')->findOneById($id);
    }
}
