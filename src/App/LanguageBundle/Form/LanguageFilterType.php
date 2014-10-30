<?php

namespace App\LanguageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LanguageFilterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isoCode','text',array("label" => "Langue / Code ISO", 'required' => false))
            ->add('enabled','checkbox', array("label" => "Activé",'required' => false));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\LanguageBundle\Entity\Language'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_language_form_language_filter';
    }
}
