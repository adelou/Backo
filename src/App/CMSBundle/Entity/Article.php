<?php

namespace App\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\CMSBundle\Entity\AbstractContent;
/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\CMSBundle\Entity\Repositories\ArticleRepository")
 */
class Article extends AbstractContent
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
     * @ORM\OneToMany(targetEntity="App\CMSBundle\Entity\ArticleMeta", mappedBy="article")
     */
    private $articleMetas;

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
        $this->articleMetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add articleMetas
     *
     * @param \App\CMSBundle\Entity\ArticleMeta $articleMetas
     * @return Article
     */
    public function addArticleMeta(\App\CMSBundle\Entity\ArticleMeta $articleMetas)
    {
        $this->articleMetas[] = $articleMetas;

        return $this;
    }

    /**
     * Remove articleMetas
     *
     * @param \App\CMSBundle\Entity\ArticleMeta $articleMetas
     */
    public function removeArticleMeta(\App\CMSBundle\Entity\ArticleMeta $articleMetas)
    {
        $this->articleMetas->removeElement($articleMetas);
    }

    /**
     * Get articleMetas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticleMetas()
    {
        return $this->articleMetas;
    }
}
