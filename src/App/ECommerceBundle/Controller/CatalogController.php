<?php

namespace App\ECommerceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\ECommerceBundle\Entity\Catalog;
use App\ECommerceBundle\Entity\Product\Product;
use App\ECommerceBundle\Form\CatalogType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Catalog controller.
 *
 * @Route("/catalog")
 */
class CatalogController extends Controller
{

    /**
     * Lists all Catalog entities.
     *
     * @Route("/", name="catalog")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            
            $oCatalogRepository = $em->getRepository('AppECommerceBundle:Catalog');

            // Recuperation des catalogues 
            $result =  $oCatalogRepository->getResult($request, $this->container);
            // Recuperation du nombre de catalogue
            $total  =  $oCatalogRepository->getTotal($request);
             
            // Recuperation de tout les catalogues et transmission des id au repository 
            $oCatalogRepository = $em->getRepository('AppECommerceBundle:Catalog');
            $aAllCatalog = $oCatalogRepository->findAll();
           
            //this doesn't work
            $output = array(
                "sEcho" => intval($request->get('sEcho')),
                "iTotalRecords" => intval($total),
                "iTotalDisplayRecords" => intval($total),
                "aaData" => array()
            );
            foreach ($result as $e) {
                $row   = array();
                $row[] = (string) $e->getId();
                $row[] = (string) $e->getName();
                $row[] = (string) count($e->getProducts());
                $row[] = (string) count($e->getCustomers());
                $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("catalog_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
                $output['aaData'][] = $row ;
            }
            
            $response = new JsonResponse();
            $response->setData($output);

            return $response;
        }
    }
    /**
     * Creates a new Catalog entity.
     *
     * @Route("/", name="catalog_create")
     * @Method("POST")
     * @Template("AppECommerceBundle:Catalog:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Catalog();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('catalog_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Catalog entity.
     *
     * @param Catalog $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Catalog $entity)
    {
        $form = $this->createForm(new CatalogType(), $entity, array(
            'action' => $this->generateUrl('catalog_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Catalog entity.
     *
     * @Route("/new", name="catalog_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Catalog();
        
        $produit1 = new Product();
        $produit1->setName('toto1');
        
        $entity->getProducts()->add($produit1);
        
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Catalog entity.
     *
     * @Route("/{id}", name="catalog_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Catalog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Catalog entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Catalog entity.
     *
     * @Route("/{id}/edit", name="catalog_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Catalog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Catalog entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Catalog entity.
    *
    * @param Catalog $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Catalog $entity)
    {
        $form = $this->createForm(new CatalogType(), $entity, array(
            'action' => $this->generateUrl('catalog_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Catalog entity.
     *
     * @Route("/{id}", name="catalog_update")
     * @Method("PUT")
     * @Template("AppECommerceBundle:Catalog:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Catalog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Catalog entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('catalog_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Catalog entity.
     *
     * @Route("/{id}", name="catalog_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:Catalog')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Catalog entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('catalog'));
    }

    /**
     * Creates a form to delete a Catalog entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('catalog_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
