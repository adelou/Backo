<?php


namespace App\MediaBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MediaExtension extends \Twig_Extension
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
            'formatImage' => new \Twig_SimpleFilter('formatImage', array($this, 'formatImage')),
        );
    }

    public function formatImage($id, $cropformat = "", $width = null)
    {
        $media = $this->container->get('doctrine')->getRepository('AppMediaBundle:Media')->find($id);
        $rootPath = $this->container->get('request')->getBasePath();
        if (is_file ($rootPath."/uploads/medias/".$cropformat.$media->getPath())) {
            return '<img src="'.$rootPath.'/uploads/medias/'.$cropformat.$media->getPath().'" id="target" />';
        } else {
            return '<img width="'.$width.'" src="'.$rootPath.'/uploads/medias/'.$media->getPath().'" id="target" />';
        }
    }

    public function getName()
    {
        return 'media_extension';
    }

}
