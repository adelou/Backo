<?php
namespace App\AdminBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminService
{
    /*
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function getDatatableValuesArray($parameters, $columns, $repo) {
        $qb = $this->_container->get('doctrine')->getRepository($repo)->createQueryBuilder('a');
        $qb->select('a');

        $qb_count = clone $qb;
        $qb->setFirstResult($parameters['start']);
        $qb->setMaxResults($parameters['limit']);
        $qb->orderBy('a.'.$columns[$parameters['sortCol']], $parameters['sortDir']);
        $result =  $qb->getQuery()->getResult();

        /* Query Count */
        $qb_count->select('COUNT(a)');
        $total =  $qb_count->getQuery()->getSingleScalarResult();

        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => intval($total),
            "iTotalDisplayRecords" => intval($total),
            "aaData" => array()
        );

        $response = array('output' => $output, 'result' => $result );

        return $response;

    }
}