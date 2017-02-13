<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationUser
 *
 * @ORM\Table(name="evaluation_user")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationUserRepository")
 */
class EvaluationUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", nullable=true)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserFacebookId", type="string", nullable=true)
     */
    private $createUserFacebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserFacebookLocale", type="string", nullable=true)
     */
    private $createUserFacebookLocale;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserFacebookRef", type="string", nullable=true)
     */
    private $createUserFacebookRef;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserFacebookTimezone", type="string", nullable=true)
     */
    private $createUserFacebookTimezone;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserIp", type="string", length=255, nullable=true)
     */
    private $createUserIp;

    /**
     * @var string
     *
     * @ORM\Column(name="createUserMail", type="string", length=255, nullable=true)
     */
    private $createUserMail;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var string
     *
     * @ORM\Column(name="HTTPUSERAGENT", type="string", length=255, nullable=true)
     */
    protected $HTTP_USER_AGENT;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createUserFacebookId
     *
     * @param string $createUserFacebookId
     *
     * @return EvaluationUser
     */
    public function setCreateUserFacebookId($createUserFacebookId)
    {
        $this->createUserFacebookId = $createUserFacebookId;

        return $this;
    }

    /**
     * Get createUserFacebookId
     *
     * @return string
     */
    public function getCreateUserFacebookId()
    {
        return $this->createUserFacebookId;
    }

    /**
     * Set createUserFacebookLocale
     *
     * @param string $createUserFacebookLocale
     *
     * @return EvaluationUser
     */
    public function setcreateUserFacebookLocale($createUserFacebookLocale)
    {
        $this->createUserFacebookLocale = $createUserFacebookLocale;

        return $this;
    }

    /**
     * Get createUserFacebookLocale
     *
     * @return string
     */
    public function getcreateUserFacebookLocale()
    {
        return $this->createUserFacebookLocale;
    }

    /**
     * Set createUserFacebookRef
     *
     * @param string $createUserFacebookRef
     *
     * @return EvaluationUser
     */
    public function setCreateUserFacebookRef($createUserFacebookRef)
    {
        $this->createUserFacebookRef = $createUserFacebookRef;

        return $this;
    }

    /**
     * Get createUserFacebookRef
     *
     * @return string
     */
    public function getCreateUserFacebookRef()
    {
        return $this->createUserFacebookRef;
    }

    /**
     * Set createUserFacebookTimezone
     *
     * @param string $createUserFacebookTimezone
     *
     * @return EvaluationUser
     */
    public function setCreateUserFacebookTimezone($createUserFacebookTimezone)
    {
        $this->createUserFacebookTimezone = $createUserFacebookTimezone;

        return $this;
    }

    /**
     * Get createUserFacebookTimezone
     *
     * @return string
     */
    public function getCreateUserFacebookTimezone()
    {
        return $this->createUserFacebookTimezone;
    }

    /**
     * Set createUserIp
     *
     * @param string $createUserIp
     *
     * @return EvaluationUser
     */
    public function setCreateUserIp($createUserIp)
    {
        $this->createUserIp = $createUserIp;

        return $this;
    }

    /**
     * Get createUserIp
     *
     * @return string
     */
    public function getCreateUserIp()
    {
        return $this->createUserIp;
    }

    /**
     * Set createUserMail
     *
     * @param string $createUserMail
     *
     * @return EvaluationUser
     */
    public function setCreateUserMail($createUserMail)
    {
        $this->createUserMail = $createUserMail;

        return $this;
    }

    /**
     * Get createUserMail
     *
     * @return string
     */
    public function getCreateUserMail()
    {
        return $this->createUserMail;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return EvaluationUser
     */
    public function setcreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getcreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set hTTPUSERAGENT
     *
     * @param string $hTTPUSERAGENT
     *
     * @return EvaluationUser
     */
    public function setHTTPUSERAGENT($hTTPUSERAGENT)
    {
        $this->HTTP_USER_AGENT = $hTTPUSERAGENT;

        return $this;
    }

    /**
     * Get hTTPUSERAGENT
     *
     * @return string
     */
    public function getHTTPUSERAGENT()
    {
        return $this->HTTP_USER_AGENT;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return EvaluationUser
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }
}
