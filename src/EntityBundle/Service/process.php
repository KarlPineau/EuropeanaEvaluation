<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityProperty;

class process
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function checkIsset($europeana_id)
    {
        return (count($this->em->getRepository('EntityBundle:EntityProperty')->findBy(array('europeanaId' => $europeana_id))) > 0) ? true : false;
    }

    public function getEuropeanaId($uri)
    {
        return preg_replace('/http:\/\/www.europeana.eu\/portal\/fr\/record(\/90402\/RP_P_OB_27_072).html/i', '$1', $uri);

    }

    public function getRecord($europeana_id)
    {
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

    public function buildRecord($record)
    {
        $object = array();

        $object['europeana_id']     = $this->getProperty($record, "about");
        $object['edmDatasetName']   = $this->getProperty($record, "edmDatasetName");
        $object['language']         = $this->getProperty($record, "language");
        $object['edmDataProvider']  = $this->getProperty($this->getAggregation($record), "edmDataProvider");
        $object['edmIsShownAt']     = $this->getProperty($this->getAggregation($record), "edmIsShownAt");
        $object['edmIsShownBy']     = $this->getProperty($this->getAggregation($record), "edmIsShownBy");
        $object['edmProvider']      = $this->getProperty($this->getAggregation($record), "edmProvider");
        $object['edmRights']        = $this->getProperty($this->getAggregation($record), "edmRights");
        $object['dcDescription']    = $this->getProperty($this->getProxy($record, false), "dcDescription");
        $object['dcContributor']    = $this->getProperty($this->getProxy($record, false), "dcContributor");
        $object['dcCreator']        = $this->getProperty($this->getProxy($record, false), "dcCreator");
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

        return $object;
    }

    public function registerRecord($object)
    {
        $europeana_id = $object['europeana_id'];
        foreach($object as $property => $value) {
            $entityProperty = new EntityProperty();
            $entityProperty->setEuropeanaId($europeana_id);
            $entityProperty->setProperty($property);

            if($value != 'null' AND $value != null) {
                $entityProperty->setValue(json_encode($value));
            } else {
                $entityProperty->setValue(null);
            }

            $this->em->persist($entityProperty);
        }
        $this->em->flush();

        return $object;
    }

    public function process($uri)
    {
        try {
            if($this->checkIsset($this->getEuropeanaId($uri)) == false) {
                $record = $this->registerRecord($this->buildRecord($this->getRecord($this->getEuropeanaId($uri))));
                return $record;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

    }
}
