<?php

namespace APIBundle\Service;

use APIBundle\Entity\EvaluationUser;
use Doctrine\ORM\EntityManager;

class user
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
        $user = new EvaluationUser();
        $user->setContext('facebook_messenger');

        $user->setCreateUserFacebookId($user_id);
        $user->setCreateUserFacebookLocale($user_locale);
        $user->setCreateUserFacebookRef($user_ref);
        $user->setCreateUserFacebookTimezone($user_timezone);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function get($id)
    {
        return $this->em->getRepository('APIBundle:EvaluationUser')->findOneById($id);
    }

    public function getByFbId($fb_id)
    {
        return $this->em->getRepository('APIBundle:EvaluationUser')->findOneByCreateUserFacebookId($fb_id);
    }

    public function getLastSession($user)
    {
        $sessions = $this->em->getRepository('APIBundle:EvaluationSession')->findBy(array('user' => $user), array('createDate' => 'DESC'), 1);
        if(count($sessions) > 0) {
            return $sessions[0];
        } else {
            return null;
        }
    }
}
