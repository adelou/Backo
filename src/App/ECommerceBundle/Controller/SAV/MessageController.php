<?php

namespace App\ECommerceBundle\Controller\SAV;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\ECommerceBundle\Entity\SAV\Message;
use App\ECommerceBundle\Form\Type\SAV\MessageType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Message controller.
 *
 * @Route("/message")
 */
class MessageController extends Controller
{

    /**
     * Lists all Message entities.
     *
     * @Route("/", name="message")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppECommerceBundle:SAV\Message')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Message entity.
     *
     * @Route("/{id_ticket}/create", name="message_create")
     * @Method("POST")
     * @Template("AppECommerceBundle:SAV\Message:new.html.twig")
     */
    public function createAction(Request $request, $id_ticket)
    {
        $entity = new Message();
        $form = $this->createCreateForm($entity, $id_ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ticket = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id_ticket);
            $user = $this->container->get('security.context')->getToken()->getUser();
            $entity->setUser($user);
            $entity->setTicket($ticket);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ticket_new_message', array('id' => $ticket->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Message entity.
     *
     * @param Message $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Message $entity, $id_ticket)
    {
        $form = $this->createForm(new MessageType(), $entity, array(
            'action' => $this->generateUrl('message_create', array('id_ticket' => $id_ticket)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Message entity.
     *
     * @Route("/new", name="message_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Message();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }



    /**
     * Displays a form to edit an existing Message entity.
     *
     * @Route("/{id}/edit", name="message_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:SAV\Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
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
    * Creates a form to edit a Message entity.
    *
    * @param Message $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Message $entity)
    {
        $form = $this->createForm(new MessageType(), $entity, array(
            'action' => $this->generateUrl('message_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Message entity.
     *
     * @Route("/{id}", name="message_update")
     * @Method("PUT")
     * @Template("AppECommerceBundle:SAV\Message:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:SAV\Message')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('message_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Message entity.
     *
     * @Route("/{id}", name="message_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:SAV\Message')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Message entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('message'));
    }

    /**
     * Creates a form to delete a Message entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('message_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Finds and displays a Message entity.
     *
     * @Route("/getMessageAjax", name="get_message_ajax")
     * @Method("POST")
     * @Template()
     */
    public function getMessageAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:SAV\Message')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Message entity.');
            }

            $aaData = array();
            $aaData['id'] = $entity->getId();
            $aaData['email'] = $entity->getUser()->getEmail();
            $aaData['created_at'] = 'le ' . $entity->getCreatedAt()->format('d/M/Y') . ' à ' . $entity->getCreatedAt()->format('H:i');
            $aaData['content'] = $entity->getContent();

            $response = new JsonResponse();
            $response->setData($aaData);

            return $response;
        }
    }
}
