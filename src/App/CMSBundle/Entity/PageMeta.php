<?php

namespace App\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageMeta
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PageMeta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\CMSBundle\Entity\Page")
     */
    private $page;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=255)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text")
     */
    private $metaValue;


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
     * Set metaKey
     *
     * @param string $metaKey
     * @return PageMeta
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Get metaKey
     *
     * @return string 
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * Set metaValue
     *
     * @param string $metaValue
     * @return PageMeta
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;

        return $this;
    }

    /**
     * Get metaValue
     *
     * @return string 
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * Set page
     *
     * @param \App\CMSBundle\Entity\Page $page
     * @return PageMeta
     */
    public function setPage(\App\CMSBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \App\CMSBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}
