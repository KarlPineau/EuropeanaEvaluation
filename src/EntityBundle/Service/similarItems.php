<?php

namespace EntityBundle\Service;

use APIBundle\Service\log;
use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityRelation;
use MessengerBundle\Service\stringify;

class similarItems
{
    protected $em;
    protected $buzz;
    protected $log;
    protected $process;
    protected $graph;
    protected $stringify;

    public function __construct(EntityManager $EntityManager, $buzz, log $log, process $process, graph $graph, stringify $stringify)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->log = $log;
        $this->process = $process;
        $this->graph = $graph;
        $this->stringify = $stringify;
    }

    public function computeSimilarity($record, $deepLevel)
    {
        set_time_limit(0);

        foreach($this->getAlgoSI() as $algorithm) {
            $this->runProcess($record, $algorithm, $deepLevel);
        }
    }

    public function getAlgoSI()
    {
        $algorithms = array();

        $algorithms[] = 'defaultAlgorithm';

        return $algorithms;
    }

    public function runProcess($record, $algorithm, $deepLevel)
    {
        $record = (object) $record;
        $sub = $this->runAlgorithm($record, $algorithm);

        $this->log->log("------------------------------------------------------------------");
        $this->log->log("-> entity.similarItems->runProcess() -- ".date('Y/m/d h:i:s a', time()));

        if($sub != null) {
            $this->log->log("-> Europeana_id: ".$sub);

            if ($this->process->checkIsset($sub) == false) {
                $this->log->log("-> Isset item ? NO");
                $subRecord = (object) $this->process->registerRecord($this->process->buildRecord($this->process->getRecord($sub)));

                $relation = new EntityRelation();
                $relation->setEntity1($record->europeana_id);
                $relation->setEntity2($sub);
                $relation->setAlgorithm($algorithm);
                $this->em->persist($relation);
                $this->em->flush();

                if ($deepLevel > 1) {
                    $this->computeSimilarity($subRecord, $deepLevel - 1);
                }
            } else {
                $this->log->log("-> Isset item ? YES");
                $relation = new EntityRelation();
                $relation->setEntity1($record->europeana_id);
                $relation->setEntity2($sub);
                $relation->setAlgorithm($algorithm);
                $this->em->persist($relation);
                $this->em->flush();
            }
        } else {
            $this->log->log("!-> Europeana_id: NULL");
        }
    }

    public function runAlgorithm($object, $algorithm)
    {
        $this->log->log("------------------------------------------------------------------");
        $this->log->log("-> entity.similarItems->runAlgorithm() -- ".date('Y/m/d h:i:s a', time()));
        $this->buzz->getClient()->setTimeout(0);
        //Must return europeana_id
        $query = $this->{$algorithm}($object);

        $this->log->log(date('Y/m/d h:i:s a', time())." -- New Query Algo");
        $this->log->log("-> Query: ".urldecode($query));
        $this->log->log("-> QueryRefine: ".str_replace(" ", "+" , urldecode($query)));

        $queryResponse = $this->buzz->get($query);
        if($queryResponse !== FALSE) {
            $this->log->log(date('Y/m/d h:i:s a', time())." -- Succeed query");
            $this->log->log($queryResponse);

            $content = (object) json_decode($queryResponse->getContent());
            $this->log->log(json_encode($content));

            if(property_exists($content, 'items')) {
                foreach($content->items as $key => $item) {
                    if($key == 0) {
                        $this->log->log("-> Item: ".$content->items[0]->id);
                        return $content->items[0]->id;
                    }
                }
            } else {
                $this->log->log("!-> No Item");
                return null;
            }
        } else {
            $this->log->log(date('Y/m/d h:i:s a', time())." -- Failed query");
            return null;
        }


    }

    public function defaultAlgorithm($object)
    {
        $object = (object) $object;
        $q = '';

        if($object->dcType != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcType, ' OR ', true).")";
        }
        if($object->dcCreator != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcCreator, ' OR ', true).")";
        }
        if($object->dcTitle != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "title:(".$this->stringify->stringify($object->dcTitle, ' OR ', true).")";
        }
        if($object->edmDataProvider != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "DATA_PROVIDER:\"".$this->stringify->stringify($object->edmDataProvider, ' OR ', true)."\"";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' OR ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");


        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query");
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.http_build_query($data)));

        return 'https://www.europeana.eu/api/v2/search.json?'.http_build_query($data);
    }
}
