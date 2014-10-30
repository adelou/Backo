<?php



namespace App\CMSBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository {


    public function getArticleList($oRequest) {

        $qb = $this->createQueryBuilder('a');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getNb($oRequest) {

        $qb_count = $this->createQueryBuilder('a');
        $qb_count->select('COUNT(a)');
        $total =  $qb_count->getQuery()->getSingleScalarResult();

        return $total;
    }


} 