<?php

namespace App\ECommerceBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\ECommerceBundle\Form\DataTransformer\IdtoObjectTransformer;

class ProductType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $transformer = new IdtoObjectTransformer($entityManager);

        $builder
            /*->add('name', 'text', array('label' => 'Nom'))
            ->add('reference', 'text', array('label' => 'Référence'))
            ->add('quantity', 'number', array('label' => 'Quantité'))
            ->add('state', 'text', array('label' => 'Etat'))
            ->add('category', 'entity',
                array('label' => 'Catégories',
                    'class' => 'AppAdminBundle:Category',
                    'property' => 'title',
                    'multiple' => true,
                    'expanded'  => false))
            ->add('supplier', 'entity',
                array('label' => 'Fournisseur',
                    'required' => false,
                    'class' => 'AppECommerceBundle:Product\Supplier',
                    'property' => 'title',
                    'multiple' => true,
                    'expanded'  => false))
           ->add('price','collection', array('type' => new PriceType()))
           */
            ->add('name', 'text', array('label' => 'Nom', 'required' => true))
            ->add('descriptionShort', 'textarea', array('label' => 'Résumé', 'required' => false))
            ->add('description', 'textarea', array('label' => 'Description', 'required' => false))
            ->add('categories', 'entity',
                array('label' => 'Catégories',
                    'required' => false,
                    'class' => 'AppAdminBundle:Category',
                    'property' => 'title',
                    'multiple' => true,
                    'expanded'  => false))
            ->add(
                $builder->create('medias', 'collection',array(
                    'type' => 'hidden',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true))
                    ->addModelTransformer($transformer)
            )


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ECommerceBundle\Entity\Product\Product'
        ));
        $resolver->setRequired(array(
            'em',
        ));
        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_ecommercebundle_product_form_product';
    }
}
