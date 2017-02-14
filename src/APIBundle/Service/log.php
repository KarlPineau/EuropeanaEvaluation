<?php

namespace APIBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Exception\IOException;

class log
{
    protected $em;

    public function __construct(EntityManager $EntityManager)
    {
        $this->em = $EntityManager;
    }

    public function log($var, $file)
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        if(!$fs->exists('../var/logs/'.$file.'.log')) {
            $fs->dumpFile('../var/logs/'.$file.'.log', '');
        }
        $contentLog = file_get_contents('../var/logs/'.$file.'.log');
        $contentLog .= $var."\n";

        try {$fs->dumpFile('../var/logs/'.$file.'.log', $contentLog);}
        catch(IOException $e) {}
    }
}
