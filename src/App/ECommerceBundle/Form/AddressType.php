<?php

namespace App\ECommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('compagny','text',array('label' => 'Société', 'required' => false))
            ->add('firstname','text',array('label' => 'Prénom', 'required' => true))
            ->add('lastname','text',array('label' => 'Nom', 'required' => true))
            ->add('address1','text',array('label' => 'Adresse', 'required' => true))
            ->add('address2','text',array('label' => 'Adresse (2)', 'required' => false))
            ->add('postcode','text',array('label' => 'Code postal', 'required' => true))
            ->add('city','text',array('label' => 'Ville', 'required' => true))
            ->add('phone','text',array('label' => 'Téléphone', 'required' => false))
            ->add('phoneMobile','text',array('label' => 'Téléphone Mobile', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ECommerceBundle\Entity\Address'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_ecommercebundle_address';
    }
}
