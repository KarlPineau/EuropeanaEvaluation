<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityProperty;
use EntityBundle\Entity\EntityRelation;

class similarItems
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

    public function getAlgoSI()
    {
        return null;
    }

    public function runProcess($record, $algorithm, $deepLevel)
    {
        $sub = $this->runAlgorithm($record, $algorithm);

        if($this->process->checkIsset($sub) == false) {
           $subRecord = $this->process->registerRecord($this->process->buildRecord($this->process->getRecord($sub)));

           $relation = new EntityRelation();
           $relation->setEntity1($record);//Mettre europeana_id
           $relation->setEntity2($subRecord);//Mettre europeana_id
           $relation->setAlgorithm($algorithm);
           $this->em->persist($relation);
           $this->em->flush();

            if($deepLevel > 1) {
                $this->computeSimilarity($subRecord, $deepLevel-1);
            }
        } else {
            $relation = new EntityRelation();
            $relation->setEntity1($record);//Mettre europeana_id
            $relation->setEntity2($sub);//Mettre europeana_id
            $relation->setAlgorithm($algorithm);
            $this->em->persist($relation);
            $this->em->flush();
        }
    }

    public function runAlgorithm($object, $algorithm)
    {
        //Must return europeana_id
        return null;
    }

    public function computeSimilarity($record, $deepLevel)
    {
        set_time_limit(0);

        foreach($this->getAlgoSI() as $algorithm) {
            $this->runProcess($record, $algorithm, $deepLevel);
        }
    }
}
