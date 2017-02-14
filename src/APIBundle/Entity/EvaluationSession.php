<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationSession
 *
 * @ORM\Table(name="evaluation_session")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationSessionRepository")
 */
class EvaluationSession
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
     * @ORM\ManyToOne(targetEntity="APIBundle\Entity\EvaluationUser")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @var string
     * Describe the evaluation type: singleEvaluation or browseEvaluation
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="contextualized", type="boolean", nullable=true, options="default: false;")
     */
    private $contextualized;

    /**
     * @var string
     *
     * @ORM\Column(name="endSession", type="boolean", nullable=true, options="default: false;")
     */
    private $endSession;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceItem", type="string", length=255, nullable=true)
     */
    private $referenceItem;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    protected $endDate;


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
     * Set type
     *
     * @param string $type
     *
     * @return EvaluationSession
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set contextualized
     *
     * @param boolean $contextualized
     *
     * @return EvaluationSession
     */
    public function setContextualized($contextualized)
    {
        $this->contextualized = $contextualized;

        return $this;
    }

    /**
     * Get contextualized
     *
     * @return boolean
     */
    public function getContextualized()
    {
        return $this->contextualized;
    }

    /**
     * Set referenceItem
     *
     * @param string $referenceItem
     *
     * @return EvaluationSession
     */
    public function setReferenceItem($referenceItem)
    {
        $this->referenceItem = $referenceItem;

        return $this;
    }

    /**
     * Get referenceItem
     *
     * @return string
     */
    public function getReferenceItem()
    {
        return $this->referenceItem;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return EvaluationSession
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return EvaluationSession
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set user
     *
     * @param \APIBundle\Entity\EvaluationUser $user
     *
     * @return EvaluationSession
     */
    public function setUser(\APIBundle\Entity\EvaluationUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \APIBundle\Entity\EvaluationUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set endSession
     *
     * @param boolean $endSession
     *
     * @return EvaluationSession
     */
    public function setEndSession($endSession)
    {
        $this->endSession = $endSession;

        return $this;
    }

    /**
     * Get endSession
     *
     * @return boolean
     */
    public function getEndSession()
    {
        return $this->endSession;
    }
}
