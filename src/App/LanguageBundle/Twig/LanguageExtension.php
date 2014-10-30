<?php


namespace App\LanguageBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;

class LanguageExtension extends \Twig_Extension
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
            'country' => new \Twig_Filter_Method($this, 'country'),
            'language' => new \Twig_Filter_Method($this, 'language')
        );
    }


    /**
     * @param $code
     * @return mixed
     */
    public function country($code, $lang = "" )
    {
        return Intl::getRegionBundle()->getCountryName($code, $lang);
    }

    /**
    * @param $code
    * @return mixed
    */
    public function language($code, $lang = "")
    {
        if(empty($lang)) {
            return Intl::getLanguageBundle()->getLanguageName($code);
        }
        $languages = Intl::getLanguageBundle()->getLanguageNames($lang);
        if (array_key_exists($code, $languages)) {
            return $languages[$code];
        }

        return '';
    }


    public function getName()
    {
        return 'language_extension';
    }

} 