<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationSequence
 *
 * @ORM\Table(name="evaluation_sequence")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationSequenceRepository")
 */
class EvaluationSequence
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
     * @ORM\ManyToOne(targetEntity="APIBundle\Entity\EvaluationSession")
     * @ORM\JoinColumn(nullable=true)
     */
    private $session;

    /**
     * @var string
     * Describe the evaluation type: can be the first experience or the second experience
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
     * @return EvaluationSequence
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
     * @return EvaluationSequence
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
     * @return EvaluationSequence
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
     * @return EvaluationSequence
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
     * @return EvaluationSequence
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
     * Set session
     *
     * @param \APIBundle\Entity\EvaluationSession $session
     *
     * @return EvaluationSequence
     */
    public function setSession(\APIBundle\Entity\EvaluationSession $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \APIBundle\Entity\EvaluationSession
     */
    public function getSession()
    {
        return $this->session;
    }
}
