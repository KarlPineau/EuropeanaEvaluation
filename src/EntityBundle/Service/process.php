<?php

namespace EntityBundle\Service;

use APIBundle\Service\log;
use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityImage;
use EntityBundle\Entity\EntityProperty;

class process
{
    protected $em;
    protected $buzz;
    protected $log;
    protected $graph;

    public function __construct(EntityManager $EntityManager, $buzz, log $log, graph $graph)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->log = $log;
        $this->graph = $graph;
    }

    public function process($uri)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.process->process() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        try {
            $this->log->log("URI: ".$uri, 'entity');
            if($this->checkIsset($this->getEuropeanaId($uri)) == false) {
                $this->log->log("Isset Item ? NO", 'entity');
                $record = $this->registerRecord($this->buildRecord($this->getRecord($this->getEuropeanaId($uri))));
                return $record;
            } else {
                $this->log->log("Isset Item ? YES", 'entity');
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

    }

    public function registerRecord($record)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.process->registerRecord() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $europeana_id = $record['europeana_id'];
        $this->log->log("Europeana_id: ".$europeana_id, 'entity');
        foreach($record as $property => $value) {
            $entityProperty = new EntityProperty();
            $entityProperty->setEuropeanaId($europeana_id);
            $entityProperty->setProperty($property);

            if($value != 'null' AND $value != null) {
                $entityProperty->setValue(json_encode($value));
            } else {
                $entityProperty->setValue(null);
            }

            $this->log->log("EntityProperty: >".$europeana_id.">".$property.">".json_encode($value), 'entity');
            $this->em->persist($entityProperty);
        }
        $this->em->flush();

        //$this->downloadThumbnail($record);

        return $record;
    }

    public function buildRecord($record)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.process->buildRecord() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = array();

        $object['europeana_id']     = $this->getProperty($record, "about");
        $object['edmDatasetName']   = $this->getProperty($record, "edmDatasetName");
        $object['language']         = $this->getProperty($record, "language");
        $object['edmDataProvider']  = $this->getProperty($this->getAggregation($record), "edmDataProvider");
        $object['edmIsShownAt']     = $this->getProperty($this->getAggregation($record), "edmIsShownAt");
        $object['edmIsShownBy']     = $this->getProperty($this->getAggregation($record), "edmIsShownBy");
        $object['edmProvider']      = $this->getProperty($this->getAggregation($record), "edmProvider");
        $object['edmRights']        = $this->getProperty($this->getAggregation($record), "edmRights");
        $object['edmObject']        = $this->getProperty($this->getAggregation($record), "edmObject");
        $object['dcDescription']    = $this->getProperty($this->getProxy($record, false), "dcDescription");
        $object['dcContributor']    = $this->getProperty($this->getProxy($record, false), "dcContributor");
        $object['dcCreator']        = $this->getProperty($this->getProxy($record, false), "dcCreator");
        $object['dcDate']           = $this->getProperty($this->getProxy($record, false), "dcDate");
        $object['dcFormat']         = $this->getProperty($this->getProxy($record, false), "dcFormat");
        $object['dcLanguage']       = $this->getProperty($this->getProxy($record, false), "dcLanguage");
        $object['dcPublisher']      = $this->getProperty($this->getProxy($record, false), "dcPublisher");
        $object['dcSubject']        = $this->getProperty($this->getProxy($record, false), "dcSubject");
        $object['dcTitle']          = $this->getProperty($this->getProxy($record, false), "dcTitle");
        $object['dcType']           = $this->getProperty($this->getProxy($record, false), "dcType");
        $object['dctermsMedium']    = $this->getProperty($this->getProxy($record, false), "dctermsMedium");
        $object['dctermsTemporal']  = $this->getProperty($this->getProxy($record, false), "dctermsTemporal");
        $object['dctermsCreated']   = $this->getProperty($this->getProxy($record, false), "dctermsCreated");
        $object['dctermsProvenance']= $this->getProperty($this->getProxy($record, false), "dctermsProvenance");
        $object['dctermsSpatial']   = $this->getProperty($this->getProxy($record, false), "dctermsSpatial");
        $object['edmType']          = $this->getProperty($this->getProxy($record, false), "edmType");
        $object['edmPreview']       = $this->getProperty($this->getEuropeanaAggregation($record), "edmPreview");

        $this->log->log("RECORD: ".json_encode($object), 'entity');
        return $object;
    }

    public function checkIsset($europeana_id)
    {
        return (count($this->em->getRepository('EntityBundle:EntityProperty')->findBy(array('europeanaId' => $europeana_id))) > 0) ? true : false;
    }

    public function getEuropeanaId($uri)
    {
        return preg_replace('/http:\/\/www.europeana.eu\/portal\/[a-zA-Z]{1,4}\/record\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9_]+).html/i', '/$1/$2', $uri);

    }

    public function getRecord($europeana_id)
    {
        $this->buzz->getClient()->setTimeout(0);
        return json_decode($this->buzz->get('http://www.europeana.eu/api/v2/record'.$europeana_id.'.json?wskey=api2demo&profile=rich')->getContent())->object;
    }

    public function getProxy($record, $isProvider)
    {
        $providerProxy =  null;

        if(gettype($record) == 'object') {
            if(property_exists($record, 'proxies')) {
                foreach($record->proxies as $proxy) {
                    if($proxy->europeanaProxy == $isProvider) {
                        $providerProxy = $proxy;
                    }
                }
            }
        } elseif(gettype($record) == 'array') {
            if(array_key_exists('proxies', $record)) {
                foreach($record['proxies'] as $proxy) {
                    if($proxy['europeanaProxy'] == $isProvider) {
                        $providerProxy = $proxy;
                    }
                }
            }
        }

        return $providerProxy;
    }

    public function getAggregation($record)
    {
        $aggregation =  null;

        if(gettype($record) == 'object') {
            if (property_exists($record, 'aggregations')) {
                if (count($record->aggregations) == 1) {
                    $aggregation = $record->aggregations[0];
                } elseif (count($record->aggregations) > 1) {
                    $aggregation = $record->aggregations;
                }
            }
        } elseif(gettype($record) == 'array') {
            if(array_key_exists('aggregations', $record)) {
                if(count($record['aggregations']) == 1) {
                    $aggregation = $record['aggregations'][0];
                } elseif (count($record['aggregations']) > 1) {
                    $aggregation = $record['aggregations'];
                }
            }
        }

        return $aggregation;
    }

    public function getEuropeanaAggregation($record)
    {
        $europeanaAggregation =  null;

        if(gettype($record) == 'object') {
            if (property_exists($record, 'europeanaAggregation')) {
                if (count($record->europeanaAggregation) > 0) {
                    $europeanaAggregation = $record->europeanaAggregation;
                }
            }
        }

        if(gettype($record) == 'array') {
            if(array_key_exists('europeanaAggregation', $record)) {
                if(count($record['europeanaAggregation']) > 0) {
                    $europeanaAggregation = $record['europeanaAggregation'];
                }
            }
        }

        return $europeanaAggregation;
    }

    public function getProperty($object, $property)
    {
        $value =  null;

        if(gettype($object) == 'object') {
            if (property_exists($object, $property)) {
                $value = $object->{$property};
            }
        } elseif(gettype($object) == 'array') {
            if(array_key_exists($property, $object)) {
                $value = $object[$property];
            }
        }

        return $value;
    }

    public function downloadThumbnail($record)
    {
        $record = (array) $record;
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.process->downloadThumbnail() -- ".date('Y/m/d h:i:s a', time()), 'entity');
        $this->log->log("Record: ".$record['europeana_id'], 'entity');

        set_time_limit(0);
        $thumbnail = $this->graph->getThumbnail($record);
        $this->log->log("Set thumbnail as: ".$thumbnail, 'entity');

        $image = new EntityImage();
        $file = file_get_contents($thumbnail);
        $image->setFile($file);
        $filename = $image->upload($file);

        if($filename != null) {
            $entityProperty = new EntityProperty();
            $entityProperty->setEuropeanaId($record['europeana_id']);
            $entityProperty->setProperty('ee_thumbnail');
            $entityProperty->setValue($filename);
            //Maybe persist your entity if you need it
            $this->em->persist($image);
            $this->em->flush();
        }
    }
}
