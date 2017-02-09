<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityProperty
 *
 * @ORM\Table(name="entity_property")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\EntityPropertyRepository")
 */
class EntityProperty
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
     * @ORM\Column(name="europeanaId", type="string", length=255)
     */
    private $europeanaId;

    /**
     * @var string
     *
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;

    /**
     * @var string
     * 
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;


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
     * Set europeanaId
     *
     * @param string $europeanaId
     *
     * @return EntityProperty
     */
    public function setEuropeanaId($europeanaId)
    {
        $this->europeanaId = $europeanaId;

        return $this;
    }

    /**
     * Get europeanaId
     *
     * @return string
     */
    public function getEuropeanaId()
    {
        return $this->europeanaId;
    }

    /**
     * Set property
     *
     * @param string $property
     *
     * @return EntityProperty
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return EntityProperty
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
