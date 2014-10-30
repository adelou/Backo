<?php

namespace App\ECommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', 'choice', array(
                    "label" => "Titre",
                    'choices' => array('M.' => 'M.', 'Mme' => 'Mme'),
                ))
            ->add('user', new CustomerUserType(), array('label' => false))
            ->add('birthday','birthday',array('label'=>'Date de naissance','widget' => 'choice','format' => 'd MM y','years' => range(date('Y'),date('Y')-80)))
            ->add('newsletter','checkbox', array('label' => 'Lettre d\'information'))
            ->add('address','collection',array(
                    'type' => new AddressType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ECommerceBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_ecommercebundle_customer';
    }
}
