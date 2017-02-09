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
}
