<?php

namespace App\CMSBundle\Form;

use App\CMSBundle\Entity\ArticleMeta;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
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
            ->add('articleMetas', 'entity', array(
                'class' => 'AppCMSBundle:ArticleMeta',
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
            'data_class' => 'App\CMSBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_cmsbundle_article';
    }
}
