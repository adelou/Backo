<?php

namespace App\ECommerceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends EntityRepository
{
    public function getCustomerByEmail($email) {
        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.user','u');
        $qb->where('u.email = :email');
        $qb->setParameter('email',$email);
        return $qb->getQuery()->getOneOrNullResult();
    }

}
