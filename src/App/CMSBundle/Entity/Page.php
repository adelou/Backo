<?php

namespace App\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\CMSBundle\Entity\AbstractContent;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\CMSBundle\Entity\Repositories\PageRepository")
 */
class Page extends AbstractContent
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
     * @ORM\OneToMany(targetEntity="App\CMSBundle\Entity\PageMeta", mappedBy="page")
     */
    private $pageMetas;

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
     * Constructor
     */
    public function __construct()
    {
        $this->pageMetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pageMetas
     *
     * @param \App\CMSBundle\Entity\PageMeta $pageMetas
     * @return Page
     */
    public function addPageMeta(\App\CMSBundle\Entity\PageMeta $pageMetas)
    {
        $this->pageMetas[] = $pageMetas;

        return $this;
    }

    /**
     * Remove pageMetas
     *
     * @param \App\CMSBundle\Entity\PageMeta $pageMetas
     */
    public function removePageMeta(\App\CMSBundle\Entity\PageMeta $pageMetas)
    {
        $this->pageMetas->removeElement($pageMetas);
    }

    /**
     * Get pageMetas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageMetas()
    {
        return $this->pageMetas;
    }
}
