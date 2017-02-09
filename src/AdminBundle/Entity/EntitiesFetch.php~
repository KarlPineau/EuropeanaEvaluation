<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntitiesFetch
 *
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\EntitiesFetchRepository")
 */
class EntitiesFetch
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
     * @ORM\ManyToMany(targetEntity="EntityBundle\Entity\EntityFetch", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $entitiesFetch;


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
     * Constructor
     */
    public function __construct()
    {
        $this->entitiesFetch = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add entitiesFetch
     *
     * @param \EntityBundle\Entity\EntityFetch $entitiesFetch
     *
     * @return EntitiesFetch
     */
    public function addEntitiesFetch(\EntityBundle\Entity\EntityFetch $entitiesFetch)
    {
        $this->entitiesFetch[] = $entitiesFetch;

        return $this;
    }

    /**
     * Remove entitiesFetch
     *
     * @param \EntityBundle\Entity\EntityFetch $entitiesFetch
     */
    public function removeEntitiesFetch(\EntityBundle\Entity\EntityFetch $entitiesFetch)
    {
        $this->entitiesFetch->removeElement($entitiesFetch);
    }

    /**
     * Get entitiesFetch
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntitiesFetch()
    {
        return $this->entitiesFetch;
    }
}
