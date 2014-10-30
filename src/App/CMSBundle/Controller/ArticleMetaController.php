<?php

namespace App\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CMSBundle\Entity\ArticleMeta;
use App\CMSBundle\Form\ArticleMetaType;

/**
 * ArticleMeta controller.
 *
 * @Route("/articlemeta")
 */
class ArticleMetaController extends Controller
{

    /**
     * Lists all ArticleMeta entities.
     *
     * @Route("/", name="articlemeta")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppCMSBundle:ArticleMeta')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ArticleMeta entity.
     *
     * @Route("/", name="articlemeta_create")
     * @Method("POST")
     * @Template("AppCMSBundle:ArticleMeta:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ArticleMeta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('articlemeta_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ArticleMeta entity.
     *
     * @param ArticleMeta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ArticleMeta $entity)
    {
        $form = $this->createForm(new ArticleMetaType(), $entity, array(
            'action' => $this->generateUrl('articlemeta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ArticleMeta entity.
     *
     * @Route("/new", name="articlemeta_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ArticleMeta();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ArticleMeta entity.
     *
     * @Route("/{id}", name="articlemeta_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCMSBundle:ArticleMeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticleMeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ArticleMeta entity.
     *
     * @Route("/{id}/edit", name="articlemeta_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCMSBundle:ArticleMeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticleMeta entity.');
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
    * Creates a form to edit a ArticleMeta entity.
    *
    * @param ArticleMeta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ArticleMeta $entity)
    {
        $form = $this->createForm(new ArticleMetaType(), $entity, array(
            'action' => $this->generateUrl('articlemeta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ArticleMeta entity.
     *
     * @Route("/{id}", name="articlemeta_update")
     * @Method("PUT")
     * @Template("AppCMSBundle:ArticleMeta:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCMSBundle:ArticleMeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticleMeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('articlemeta_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ArticleMeta entity.
     *
     * @Route("/{id}", name="articlemeta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppCMSBundle:ArticleMeta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ArticleMeta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('articlemeta'));
    }

    /**
     * Creates a form to delete a ArticleMeta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('articlemeta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
