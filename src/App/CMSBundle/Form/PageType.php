<?php

namespace App\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('content', 'text')
            ->add('slug', 'text')
            ->add('title', 'text')
            ->add('pageMetas', 'entity', array(
                'class' => 'AppCMSBundle:PageMeta',
                'expanded' => false,
                'multiple' => true
            ))
            ->add('publishedAt', 'date', array(
                'input'  => 'datetime',
                'widget' => 'choice'))
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CMSBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_cmsbundle_page';
    }
}
