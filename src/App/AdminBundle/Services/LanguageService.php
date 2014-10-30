<?php
namespace App\AdminBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LanguageService
{
    /*
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function getCurrent() {
        
        $lang = $this->_container->get('request')->query->get('lang','');
        if(empty($lang)) {
            $lang = $this->_container->getParameter('locale');
        }

        return $lang;
    }
}