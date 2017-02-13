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

    public function log($var)
    {
        /* LOGGING */
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        if(!$fs->exists('logs.text')) {
            $fs->dumpFile('logs.text', '');
        }
        $contentLog = file_get_contents('logs.text');
        $contentLog .= $var."\n";
        try {$fs->dumpFile('logs.text', $contentLog);}
        catch(IOException $e) {}
        /* -- END LOGGING */
    }
}
