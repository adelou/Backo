<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Form\Type;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupFormType extends AbstractType
{

    protected $roles;

    public function __construct(Container $container)
    {
        $this->roles =  $container->get('app.userbundle.services.roles')->getRoles();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array('label' => 'Nom'))
        ->add('roles', 'choice', array(
        'label' => 'Roles',
        'choices' => $this->roles,
        'expanded' => true,
        'multiple' => true,
        'required' => false
        ))
        ->add('submit', 'submit', array('label' => 'Modifier'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\UserBundle\Entity\Group'
        ));
    }

    public function getParent()
    {
        return 'fos_user_group';
    }


    public function getName()
    {
        return 'app_userbundle_form_group';
    }
}
