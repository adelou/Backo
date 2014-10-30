<?php

namespace App\CMSBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\CMSBundle\Entity\AbstractContent;
use App\CMSBundle\Entity\Article;
use App\CMSBundle\Entity\Page;

/**
 * Description of ContentFactory
 *
 * @author optimus
 */
class ContentFactory {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $oContainer;

    public function __construct(ContainerInterface $oContainer)
    {
        $this->oContainer     = $oContainer;
    }
    
    public function getFactoryTest($oObjectFactory) {
        
        $aClassProduted = array('article' => new Article(), 'page' => new Page());
        
        $this->oContainer = $aClassProduted[$oObjectFactory];

        
        return $this->oContainer;
        
    }
}
