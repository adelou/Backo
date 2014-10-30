<?php


namespace App\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminExtension extends \Twig_Extension
{
    /*
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
     protected $container;
     
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(

        );
    }

    public function getName()
    {
        return 'admin_extension';
    }

} 