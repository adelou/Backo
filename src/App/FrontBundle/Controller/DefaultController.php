<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\AdminBundle\Entity\HermesUser;
use Symfony\Component\HttpFoundation\Response;
use App\AdminBundle\Entity\QuestionUserHermes;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="front")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

}
