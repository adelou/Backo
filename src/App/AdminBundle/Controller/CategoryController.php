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
 * @Route("/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppAdminBundle:Category')->findAll();
        if(empty($entities)) {
            $root = new Category();
            $root->setTitle('root');
            $em->persist($root);
            $em->flush();
        }

        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>'
        );
        $htmlTree = $em->getRepository('AppAdminBundle:Category')->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            array(
                'decorate' => true,
                'representationField' => 'title',
                'html' => true,
                'rootOpen' => '<ul>',
                'rootClose' => '</ul>',
                'childOpen' => function($child) {
                    return '<li id="'.$child['id'].'">';
                },
                'childClose' => '</li>'
            )
        );
        return array(
            'htmlTree' => $htmlTree,
        );
    }
    /**
     * Save a new Category entity.
     *
     * @Route("/", name="category_save")
     * @Method("POST")
     * @Template()
     */
    public function saveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id','');
            $name = $request->request->get('name','');
            $action = $request->request->get('action','');
            $parentid = $request->request->get('parentid','');
            $position = $request->request->get('position','');
            $old_position = $request->request->get('old_position','');
            $category = null;

            switch ($action) {
                case "create" :
                    $parentCategory = $em->getRepository('AppAdminBundle:Category')->find($id);
                    if($parentCategory) {
                        $category = new Category();
                        $category->setTitle($name);
                        $category->setParent($parentCategory);
                        $em->persist($category);
                        $em->flush();
                    }
                    break;
                case "rename" :
                    $category = $em->getRepository('AppAdminBundle:Category')->find($id);
                    if($category) {
                        $category->setTitle($name);
                        $em->persist($category);
                        $em->flush();
                    }
                    break;
                case "delete" :
                    $category = $em->getRepository('AppAdminBundle:Category')->find($id);
                    $em->remove($category);
                    $em->flush();
                    break;
                case "move" :
                    $category = $em->getRepository('AppAdminBundle:Category')->find($id);
                    $parentCategory = $em->getRepository('AppAdminBundle:Category')->find($parentid);
                    $category->setParent($parentCategory);
                    $em->persist($category);
                    $em->flush();
                    $move = (int) $position - (int) $old_position;
                    if($move < 0){
                        $move =  abs($move);
                        $em->getRepository('AppAdminBundle:Category')->moveUp($category, $move);
                    } else if($move >0) {
                        $em->getRepository('AppAdminBundle:Category')->moveDown($category, $move);
                    }
                    break;
            }

            if($category) {
                $id = $category->getId();
            } else {
                $id = null;
            }

            $response = new JsonResponse();
            $response->setData(array('id' => $id));
            return $response;
        }
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/", name="category_create")
     * @Method("POST")
     * @Template("AppAdminBundle:Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
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
    * Creates a form to edit a Category entity.
    *
    * @param Category $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}", name="category_update")
     * @Method("PUT")
     * @Template("AppAdminBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAdminBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
