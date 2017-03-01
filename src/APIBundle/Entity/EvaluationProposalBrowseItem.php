<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationProposalBrowseItem
 *
 * @ORM\Table(name="evaluation_proposal_browse_item")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationProposalBrowseItemRepository")
 */
class EvaluationProposalBrowseItem
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
     * @ORM\ManyToOne(targetEntity="APIBundle\Entity\EvaluationProposalBrowse")
     * @ORM\JoinColumn(nullable=true)
     */
    private $proposal;

    /**
     * @var string
     *
     * @ORM\Column(name="item", type="string", length=255)
     */
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="algorithm", type="string", length=255)
     */
    private $algorithm;
    /**
     * @var int
     *
     * @ORM\Column(name="orderValue", type="integer", nullable=true)
     */
    private $orderValue;

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
     * Set item
     *
     * @param string $item
     *
     * @return EvaluationProposalItem
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set proposal
     *
     * @param \APIBundle\Entity\EvaluationProposalBrowse $proposal
     *
     * @return EvaluationProposalBrowseItem
     */
    public function setProposal(\APIBundle\Entity\EvaluationProposalBrowse $proposal = null)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \APIBundle\Entity\EvaluationProposalBrowse
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * Set algorithm
     *
     * @param string $algorithm
     *
     * @return EvaluationProposalBrowseItem
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get algorithm
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return EvaluationProposalBrowseItem
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
     * Set orderValue
     *
     * @param integer $orderValue
     *
     * @return EvaluationProposalBrowseItem
     */
    public function setOrderValue($orderValue)
    {
        $this->orderValue = $orderValue;

        return $this;
    }

    /**
     * Get orderValue
     *
     * @return integer
     */
    public function getOrderValue()
    {
        return $this->orderValue;
    }
}
