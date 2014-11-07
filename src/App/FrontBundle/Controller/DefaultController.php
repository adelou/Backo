<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\AdminBundle\Entity\HermesUser;
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
