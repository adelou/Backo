<?php

namespace App\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CropingType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',array('label' => 'Nom'))
            ->add('width','integer',array('label' => 'Largeur', 'data' => 500))
            ->add('height','integer',array('label' => 'Hauteur', 'data' => 500))
            ->add('quality','integer',array('label' => 'Qualité', 'data' => 100))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\MediaBundle\Entity\Croping'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_adminbundle_croping';
    }
}
