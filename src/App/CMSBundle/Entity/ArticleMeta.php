<?php

namespace App\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleMeta
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ArticleMeta
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
     * @ORM\ManyToOne(targetEntity="App\CMSBundle\Entity\Article")
     */
    private $article;
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
     * @return ArticleMeta
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
     * @return ArticleMeta
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
     * Set article
     *
     * @param \App\CMSBundle\Entity\Article $article
     * @return ArticleMeta
     */
    public function setArticle(\App\CMSBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \App\CMSBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }

    public function __toString() {

        return $this->metaKey;
    }
}
