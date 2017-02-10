<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityProperty;

class graph
{
    protected $em;
    protected $buzz;
    protected $process;

    public function __construct(EntityManager $EntityManager, $buzz, process $process)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->process = $process;
    }

    public function buildObject($uri)
    {
        $object = array();

        foreach($this->em->getRepository('EntityBundle:EntityProperty')->findBy(array('europeanaId' => $this->process->getEuropeanaId($uri))) as $EntityProperty) {
            $object[$EntityProperty->getProperty()] = json_decode($EntityProperty->getValue());
        }

        return $object;
    }

    public function buildObjectById($europeana_id)
    {
        $object = array();

        foreach($this->em->getRepository('EntityBundle:EntityProperty')->findBy(array('europeanaId' => $europeana_id)) as $EntityProperty) {
            $object[$EntityProperty->getProperty()] = json_decode($EntityProperty->getValue());
        }

        return $object;
    }
}
