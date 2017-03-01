<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityProperty;
use MessengerBundle\Service\stringify;

class graph
{
    protected $em;
    protected $buzz;
    protected $stringify;

    public function __construct(EntityManager $EntityManager, $buzz, stringify $stringify)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->stringify = $stringify;
    }

    public function buildObject($europeana_id)
    {
        $object = array();

        foreach($this->em->getRepository('EntityBundle:EntityProperty')->findBy(array('europeanaId' => $europeana_id)) as $EntityProperty) {
            $object[$EntityProperty->getProperty()] = json_decode($EntityProperty->getValue());
        }

        return $object;
    }

    public function url_exists($url) {
        return (!$fp = curl_init($url)) ? false : true;
    }

    public function getThumbnail($record)
    {
        if(gettype($record) == 'object') {$record = (array) $record;}

       /*if(array_key_exists('edmIsShownBy', $record) AND $record['edmIsShownBy'] != null AND $this->url_exists($this->stringify->stringify($record['edmIsShownBy'], ' OR ', false)) == true) {
            return $record['edmIsShownBy'];
        } else*/
        if(array_key_exists('edmObject', $record) AND $record['edmObject'] != null AND $this->url_exists($this->stringify->stringify($record['edmObject'], ' OR ', false)) == true) {
            return $record['edmObject'];
        } elseif(array_key_exists('edmPreview', $record) AND $record['edmPreview'] != null AND $this->url_exists($this->stringify->stringify($record['edmPreview'], ' OR ', false)) == true) {
            return $record['edmPreview'];
        } else {
            return 'http://europeana-evaluation.karl-pineau.fr/web/images/no_image_available.png';
        }
    }

    public function getRelations($id)
    {
        $ids = [];
        foreach($this->em->getRepository('EntityBundle:EntityRelation')->findBy(array('entity1' => $id)) as $relation) {
            $ids[] = ['entity' => $relation->getEntity2(), 'algorithm' => $relation->getAlgorithm()];
        }

        return $ids;
    }

    public function getLevel($id, $level=null)
    {
        $level = ($level == null) ? 0 : $level;

        $relation = $this->em->getRepository('EntityBundle:EntityRelation')->findOneBy(array('entity2' => $id), array('createDate', 'ASC'));
        if($relation == null) {
            return $level;
        } else {
            $level++;
            return $this->getLevel($relation->getEntity1(), $level);
        }
    }
}
