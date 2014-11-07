<?php

namespace App\MediaBundle\Entity;

use App\MediaBundle\Lib\GlobalsMedia;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\AdminBundle\Entity\AbstractDefault;
use App\MediaBundle\Lib\Globals;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Media extends AbstractDefault
{
    
    const PICTURE_WIDTH_SIZE_MAX = 500;
    const PLACEMENT_VALIDATION_DELAY_ID = 1;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
    * @ORM\ManyToMany(targetEntity="App\MediaBundle\Entity\Croping", inversedBy="media")
    */
    protected $croping;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;
    
    
    /**
     * @Assert\File(maxSize="200M")
     */
    public $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->croping = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // unique name
            //$this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
            $this->path = $this->file->getClientOriginalName();
            $this->type = $this->file->getMimeType();
            $this->extension = $this->file->guessExtension();;
            $this->name = $this->file->getClientOriginalName();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->file->move(GlobalsMedia::getUploadDir(), $this->path);

        unset($this->file);

    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = GlobalsMedia::getUploadDir().'/'.$this->path()) {
            unlink($file);
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Media
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add croping
     *
     * @param \App\MediaBundle\Entity\Croping $croping
     * @return Media
     */
    public function addCroping(\App\MediaBundle\Entity\Croping $croping)
    {
        $this->croping[] = $croping;

        return $this;
    }

    /**
     * Remove croping
     *
     * @param \App\MediaBundle\Entity\Croping $croping
     */
    public function removeCroping(\App\MediaBundle\Entity\Croping $croping)
    {
        $this->croping->removeElement($croping);
    }

    /**
     * Get croping
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCroping()
    {
        return $this->croping;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Media
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set temp
     *
     * @param string $temp
     * @return Media
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;

        return $this;
    }

    /**
     * Get temp
     *
     * @return string 
     */
    public function getTemp()
    {
        return $this->temp;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
