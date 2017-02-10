<?php

namespace EntityBundle\Service;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityProperty;
use EntityBundle\Entity\EntityRelation;

class stringify
{
    protected $em;
    protected $buzz;

    public function __construct(EntityManager $EntityManager, $buzz)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
    }

    public function stringify($array)
    {
        if(count($array) == 0) {
            return null;
        } elseif(count($array) == 1) {
            reset($array);
            return $array[key($array)];
        } elseif(count($array) > 1) {
            if (array_key_exists('def', $array)) {
                return $array['def'];
            } elseif (array_key_exists('en', $array)) {
                return $array['en'];
            } else {
                return $array[0];
            }
        }
    }
}
