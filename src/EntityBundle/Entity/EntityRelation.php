<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EntityRelation
 *
 * @ORM\Table(name="entity_relation")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\EntityRelationRepository")
 */
class EntityRelation
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
     * @ORM\Column(name="entity1", type="string", length=255)
     */
    private $entity1;

    /**
     * @var string
     *
     * @ORM\Column(name="entity2", type="string", length=255)
     */
    private $entity2;

    /**
     * @var string
     *
     * @ORM\Column(name="algorithm", type="string", length=255)
     */
    private $algorithm;

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
     * Set entity1
     *
     * @param string $entity1
     *
     * @return EntityRelation
     */
    public function setEntity1($entity1)
    {
        $this->entity1 = $entity1;

        return $this;
    }

    /**
     * Get entity1
     *
     * @return string
     */
    public function getEntity1()
    {
        return $this->entity1;
    }

    /**
     * Set entity2
     *
     * @param string $entity2
     *
     * @return EntityRelation
     */
    public function setEntity2($entity2)
    {
        $this->entity2 = $entity2;

        return $this;
    }

    /**
     * Get entity2
     *
     * @return string
     */
    public function getEntity2()
    {
        return $this->entity2;
    }

    /**
     * Set algorithm
     *
     * @param string $algorithm
     *
     * @return EntityRelation
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
     * @return EntityRelation
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
}
