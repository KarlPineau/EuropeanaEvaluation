<?php

namespace MessengerBundle\Service;

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

    public function wrap($array)
    {
        if(gettype($array) == 'object') {$array = (array) $array;}
        if(gettype($array) == 'array') {
            return array_map(
                function ($el) {
                    return "\"{$el}\"";
                },
                $array
            );
        } else {
            return $array;
        }
    }

    public function stringify($array, $delimiter, $wrapBool)
    {
        if(gettype($array) == "object") {$array = (array) $array;}

        $variable = null;
        if(gettype($array) == "array") {
            $wrap = null;
            if (count($array) == 0) {
                $wrap = null;
            }

            elseif (count($array) == 1) {
                ($wrapBool == true) ? $wrap = $this->wrap($array[key($array)]) : $wrap = $array[key($array)];
            }

            elseif (count($array) > 1) {

                if (array_key_exists('def', $array)) {
                    ($wrapBool == true) ? $wrap = $this->wrap($array['def']) : $wrap = $array['def'];
                } elseif (array_key_exists('en', $array)) {
                    ($wrapBool == true) ? $wrap = $this->wrap($array['en']) : $wrap = $array['en'];
                } else {
                    reset($array);
                    ($wrapBool == true) ? $wrap = $this->wrap($array[key($array)]) : $wrap = $array[key($array)];
                }
            }

            if(gettype($wrap) == 'array') {
                $variable = implode($delimiter, $wrap);
            } elseif(gettype($wrap) == 'object') {
                $wrap = (array) $wrap;
                $variable = implode($delimiter, $wrap);
            } elseif(gettype($wrap) == 'string') {
                $variable = $wrap;
            } else {
                $variable = $wrap;
            }

        } else {
            $variable = $array;
        }

        if(gettype($variable) == 'string' OR $variable == null) {
            return $variable;
        } else {
            $this->stringify($variable, $delimiter, $wrapBool);
        }
    }
}
