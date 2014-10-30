<?php

namespace App\ECommerceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class IdtoObjectTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (id).
     *
     * @param  ArrayCollection : $array
     * @return Array<id> : $object
     */
    public function transform($array)
    {
        // affichage
        $objects = array();
        foreach($array as $k => $a) {
           array_push($objects, $a->getId());
        }
        
       return $objects;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  array $id
     * @return object|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($array)
    {
        // submit
        $aArrayCollection = new \Doctrine\Common\Collections\ArrayCollection();

        foreach($array as $a) {
            $object = $this->om->getRepository('AppMediaBundle:Media')->find($a);
            $aArrayCollection->add($object);
        }

        return $aArrayCollection;
    }
}