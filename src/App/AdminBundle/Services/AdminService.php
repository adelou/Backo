<?php
namespace App\AdminBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdmineService
{
    /*
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function getDatatableJson() {

    }
}