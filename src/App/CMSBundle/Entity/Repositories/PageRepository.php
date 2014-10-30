<?php
/**
 * Created by PhpStorm.
 * User: optimus
 * Date: 15/10/2014
 * Time: 14:52
 */

namespace App\CMSBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;


class PageRepository extends EntityRepository {

    public function getArticleList($oRequest) {

        $qb = $this->createQueryBuilder('p');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getNb($oRequest) {

        $qb_count = $this->createQueryBuilder('p');
        $qb_count->select('COUNT(p)');
        $total =  $qb_count->getQuery()->getSingleScalarResult();

        return $total;
    }

} 