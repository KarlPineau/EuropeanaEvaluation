<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationProposalSingle
 *
 * @ORM\Table(name="evaluation_proposal_single")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationProposalSingleRepository")
 */
class EvaluationProposalSingle
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
     * @ORM\ManyToOne(targetEntity="APIBundle\Entity\EvaluationSequence")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceItem", type="string", length=255, nullable=true)
     */
    private $referenceItem;

    /**
     * @var string
     *
     * @ORM\Column(name="suggestedItem", type="string", length=255, nullable=true)
     */
    private $suggestedItem;

    /**
     * @var integer
     *
     * @ORM\Column(name="rateValue", type="integer", nullable=true)
     */
    private $rateValue;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    private $createDate;


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
     * Set referenceItem
     *
     * @param string $referenceItem
     *
     * @return EvaluationProposalSingle
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
     * Set suggestedItem
     *
     * @param string $suggestedItem
     *
     * @return EvaluationProposalSingle
     */
    public function setSuggestedItem($suggestedItem)
    {
        $this->suggestedItem = $suggestedItem;

        return $this;
    }

    /**
     * Get suggestedItem
     *
     * @return string
     */
    public function getSuggestedItem()
    {
        return $this->suggestedItem;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return EvaluationProposalSingle
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
     * Set sequence
     *
     * @param \APIBundle\Entity\EvaluationSequence $sequence
     *
     * @return EvaluationProposalSingle
     */
    public function setSequence(\APIBundle\Entity\EvaluationSequence $sequence = null)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return \APIBundle\Entity\EvaluationSequence
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set rateValue
     *
     * @param integer $rateValue
     *
     * @return EvaluationProposalSingle
     */
    public function setRateValue($rateValue)
    {
        $this->rateValue = $rateValue;

        return $this;
    }

    /**
     * Get rateValue
     *
     * @return integer
     */
    public function getRateValue()
    {
        return $this->rateValue;
    }
}
