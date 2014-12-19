<?php

namespace App\ECommerceBundle\Controller\SAV;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\ECommerceBundle\Entity\SAV\Ticket;
use App\ECommerceBundle\Form\Type\SAV\TicketType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\ECommerceBundle\Entity\SAV\Message;
use App\ECommerceBundle\Form\Type\SAV\MessageType;
use Symfony\Component\Intl\Intl;

/**
 * Ticket controller.
 *
 * @Route("/ticket")
 */
class TicketController extends Controller
{

    /**
     * Lists all Ticket entities.
     *
     * @Route("/", name="ticket")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            /* DataTable Parameters*/
            $parameters['sortCol'] = $request->get('iSortCol_0');
            $parameters['sortDir'] = $request->get('sSortDir_0');
            $parameters['filters'] = $request->get('filters');
            $parameters['start'] = $request->get('iDisplayStart');
            $parameters['limit'] = $request->get('iDisplayLength');
            $parameters['sEcho'] = $request->get('sEcho');
            $parameters['lang'] = $request->get('lang');
            if(empty($parameters['lang'])) {
                $parameters['lang'] = $this->container->getParameter('locale');
            }

            /* Columns */
            $columns = array('0' => 'id', '1' => 'name', '2' => 'type', '3' => 'state', '4' => 'content', '5' => 'created_at');

            /* DatatableValuesArray*/
            $data = $this->container->get('app.adminbundle.services.admin')->getDatatableValuesArray($parameters, $columns, 'AppECommerceBundle:SAV\Ticket');
            $data = $this->parseDatatableResult($data, $parameters);

            /* Response */
            $response = new JsonResponse();
            $response->setData($data['output']);

            return $response;
        }
    }

    protected function parseDatatableResult($data, $parameters) {

        $em = $this->getDoctrine()->getManager();

        foreach ($data['result'] as $e) {
            $message = $em->getRepository('AppECommerceBundle:SAV\Message')->getLastMessageTicket($e->getId());
            $row = array();
            $row[] = (string) $e->getId();
            $row[] = "-";
            $row[] = (string) $e->getEmail();
            $row[] = (string) $e->getType();
            $row[] = (string) $e->getState();
            if($message instanceof \App\ECommerceBundle\Entity\SAV\Message) {
                $row[] = (string) $message->getContent();
                $row[] = (string) $message->getCreatedAt()->format('d/m/Y H:i:s');
            } else {
                $row[] = '-';
                $row[] = '-';
            }
            $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("ticket_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
            $data['output']['aaData'][] = $row ;
        }
        return $data;
    }

    /**
     * Creates a new Ticket entity.
     *
     * @Route("/", name="ticket_create")
     * @Method("POST")
     * @Template("AppECommerceBundle:SAV\Ticket:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Ticket();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $data = $request->get($form->getName(), array());
        $customer = $em->getRepository('AppECommerceBundle:Customer')->getCustomerByEmail($data["email"]);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCustomer($customer);
            $entity->setState("nouveau");
            $em->persist($entity);
            $em->flush();

            $from = $data["email"];
            $to = "a.delachezemurel@novediagroup.com";
            $subject = "[SAV] Prise de contact";
            $body = $data["messages"]["content"];
            $this->get('app.adminbundle.services.mailer')->send($from, $to, $subject, $body);
            return $this->redirect($this->generateUrl('ticket'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Ticket entity.
     *
     * @param Ticket $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ticket $entity)
    {
        $form = $this->createForm(new TicketType($this->container), $entity, array(
            'action' => $this->generateUrl('ticket_create'),
            'method' => 'POST',
            'context' => $this->get('security.context')
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Ticket entity.
     *
     * @Route("/new", name="ticket_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Ticket();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Ticket entity.
     *
     * @Route("/{id}", name="ticket_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Ticket entity.
     *
     * @Route("/{id}/edit", name="ticket_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
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
    * Creates a form to edit a Ticket entity.
    *
    * @param Ticket $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ticket $entity)
    {
        $form = $this->createForm(new TicketType(), $entity, array(
            'action' => $this->generateUrl('ticket_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'context' => $this->get('security.context')
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Ticket entity.
     *
     * @Route("/{id}", name="ticket_update")
     * @Method("PUT")
     * @Template("AppECommerceBundle:SAV\Ticket:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ticket_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Ticket entity.
     *
     * @Route("/{id}", name="ticket_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ticket entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ticket'));
    }

    /**
     * Creates a form to delete a Ticket entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticket_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Displays all ticket's messages and create a new message.
     *
     * @Route("/{id}/ticket-new-message", name="ticket_new_message")
     * @Method("GET")
     * @Template("AppECommerceBundle:SAV\Ticket:newMessage.html.twig")
     */
    public function newMessageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $ticket = $em->getRepository('AppECommerceBundle:SAV\Ticket')->find($id);

        $entity = new Message();
        $form = $this->createForm(new MessageType(), $entity, array(
                'action' => $this->generateUrl('message_create', array('id_ticket' => $ticket->getId())),
                'method' => 'POST',
            ));

        $form->add('submit', 'submit', array('label' => 'Envoyer'));

        return array(
            'ticket' => $ticket,
            'messages' => $em->getRepository('AppECommerceBundle:SAV\Message')->getMessagesTicket($ticket->getId()),
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }
}
