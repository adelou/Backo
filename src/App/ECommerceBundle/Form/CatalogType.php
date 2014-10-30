<?php

namespace App\ECommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\ECommerceBundle\Form\DataTransformer\IdtoObjectTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\ECommerceBundle\Form\Product\ProductType;

class CatalogType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
            $builder->add('name')
//            ->add('products', 'entity', array(
//                'class' => "App\ECommerceBundle\Entity\Product\Product",
//            ))
                
            ->add('products', 'collection', array(
                'label' => 'labelName',
                'type' => new ProductType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
//                
//            ->add(
//                $builder->create('products', 'collection',array(
//                    'type' => 'hidden',
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'by_reference' => false,
//                    'prototype' => true))
//                    ->addModelTransformer($transformer)
//            )    
                
            ->add('customers')
            ->add('country')
        ;
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ECommerceBundle\Entity\Catalog'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_ecommercebundle_catalog';
    }
}
