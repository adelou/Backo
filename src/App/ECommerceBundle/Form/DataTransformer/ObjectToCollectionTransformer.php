<?php

namespace App\ECommerceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class ObjecttoCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Transforms an object (issue) to a string (id).
     *
     * @param  Object|null $object
     * @return string
     */
    public function transform($array)
    {
        $object = null;
        foreach($array as $a) {
            $object = $a;
        }
        return $object;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  array $id
     * @return object|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($object)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $object->setUser($user);
        $array = new \Doctrine\Common\Collections\ArrayCollection();
        $array->add($object);

        return $array;
    }
}