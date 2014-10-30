<?php

namespace App\ECommerceBundle\Entity\Product;

use App\AdminBundle\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use App\MediaBundle\Entity\Media;
use Gedmo\Translatable\Translatable;
use App\AdminBundle\Entity\AbstractDefault;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity()
 */
class Product extends AbstractDefault implements Translatable
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
     * @ORM\ManyToMany(targetEntity="App\AdminBundle\Entity\Category", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\MediaBundle\Entity\Media", cascade={"persist"})
     */
    private $medias;

    /**
     * @ORM\ManyToMany(targetEntity="App\ECommerceBundle\Entity\Catalog", cascade={"persist"})
     */
    private $catalogs;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="description_short", type="text")
     */
    private $description_short;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    
    
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
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

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description_short
     */
    public function setDescriptionShort($description_short)
    {
        $this->description_short = $description_short;
    }

    /**
     * @return string
     */
    public function getDescriptionShort()
    {
        return $this->description_short;
    }


    public function getMedias()
    {
        return $this->medias;
    }
    public function setMedias(\Doctrine\Common\Collections\ArrayCollection $medias)
    {
        foreach ($medias as $media) {
            $media->addProduct($this);
        }
        $this->medias = $medias;
    }
    public function removeMedias(Media $media)
    {
        $this->medias->removeElement($media);
    }


    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
        return $this;
    }

    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    public function getCategories()
    {
        return $this->categories;
    }
    
    public function __toString()
    {
        return $this->name;
    }

}
