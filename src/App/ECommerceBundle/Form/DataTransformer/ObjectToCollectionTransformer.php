<?php

namespace App\ECommerceBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ObjecttoCollectionTransformer implements DataTransformerInterface
{


    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
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

        $user = $this->securityContext->getToken()->getUser();
        $object->setUser($user);
        $array = new \Doctrine\Common\Collections\ArrayCollection();
        $array->add($object);

        return $array;
    }

}
