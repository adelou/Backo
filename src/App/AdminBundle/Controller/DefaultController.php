<?php

namespace App\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AdminBundle\Entity\Category;
use App\AdminBundle\Form\CategoryType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Category controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/dashboard", name="dashboard")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

}
