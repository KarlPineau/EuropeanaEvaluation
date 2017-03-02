<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * EntityImage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\EntityImageRepository")
 */
class EntityImage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    protected $file;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setFile(File $myFile = null)
    {
        $this->file = $myFile;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function upload(File $file)
    {
        //generate unique filename
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        //Set other entity attribute here

        //move the file
        $file->move('./web/media/thumbnails/', $fileName);

        return $fileName;
    }
}
