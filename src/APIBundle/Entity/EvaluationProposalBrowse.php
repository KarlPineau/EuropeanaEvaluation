<?php

namespace APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EvaluationProposalBrowse
 *
 * @ORM\Table(name="evaluation_proposal_browse")
 * @ORM\Entity(repositoryClass="APIBundle\Repository\EvaluationProposalBrowseRepository")
 */
class EvaluationProposalBrowse
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
     * @ORM\ManyToOne(targetEntity="APIBundle\Entity\EvaluationProposalBrowseItem")
     * @ORM\JoinColumn(nullable=true)
     */
    private $choicedItem;

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
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return EvaluationProposal
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
     * Set session
     *
     * @param \APIBundle\Entity\EvaluationSession $session
     *
     * @return EvaluationProposal
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

    /**
     * Set choicedItem
     *
     * @param \APIBundle\Entity\EvaluationProposalBrowseItem $choicedItem
     *
     * @return EvaluationProposal
     */
    public function setChoicedItem(\APIBundle\Entity\EvaluationProposalBrowseItem $choicedItem = null)
    {
        $this->choicedItem = $choicedItem;

        return $this;
    }

    /**
     * Get choicedItem
     *
     * @return \APIBundle\Entity\EvaluationProposalBrowseItem
     */
    public function getChoicedItem()
    {
        return $this->choicedItem;
    }
}
