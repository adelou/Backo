<?php

namespace App\ECommerceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\ECommerceBundle\Entity\Customer;
use App\ECommerceBundle\Form\Type\CustomerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Intl\Intl;

/**
 * Customer controller.
 *
 * @Route("/customer")
 */
class CustomerController extends Controller
{

    /**
     * Lists all Customer entities.
     *
     * @Route("/", name="customer")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            /* DataTable Parameters*/
            $parameters['sortCol'] = $request->get('iSortCol_0');
            $parameters['sortDir'] = $request->get('iSortDir_0');
            $parameters['filters'] = $request->get('filters');
            $parameters['start'] = $request->get('iDisplayStart');
            $parameters['limit'] = $request->get('iDisplayLength');
            $parameters['sEcho'] = $request->get('sEcho');
            $parameters['lang'] = $request->get('lang');
            if(empty($parameters['lang'])) {
                $parameters['lang'] = $this->container->getParameter('locale');
            }

            /* Columns */
            $columns = array('0' => 'id', '1' => 'gender', '2' => 'firstname', '3' => 'lastname', '4' => 'email', '5' => 'newsletter', '6' => 'lastLogin');

            /* DatatableValuesArray*/
            $data = $this->container->get('app.adminbundle.services.admin')->getDatatableValuesArray($parameters, $columns, 'AppECommerceBundle:Customer');
            $data = $this->parseDatatableResult($data, $parameters);

            /* Response */
            $response = new JsonResponse();
            $response->setData($data['output']);

            return $response;
        }
    }

    protected function parseDatatableResult($data, $parameters) {

        foreach ($data['result'] as $e) {
            $row   = array();
            $row[] = (string) $e->getId();
            $row[] = (string) $e->getGender();
            $row[] = (string) $e->getUser()->getFirstname();
            $row[] = (string) $e->getUser()->getLastname();
            $row[] = (string) $e->getUser()->getEmail();
            $row[] = (string) $e->getNewsletter();
            if(is_object($e->getUser()->getLastLogin())) {
                $row[] = (string) $e->getUser()->getLastLogin()->format('d/M/Y H:m:i');
            } else {
                $row[] = "-";
            }
            $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("customer_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';$row = array();
            $row[] = (string) $e->getId();
            $row[] = (string) $e->getPosition();
            $languages = Intl::getLanguageBundle()->getLanguageNames($parameters['lang']);
            (array_key_exists($e->getIsoCode(), $languages)) ? $row[] = (string) $languages[$e->getIsoCode()] : $row[] = "";
            $row[] = (string) $e->getIsoCode();
            ($e->getEnabled()==0) ? $row[] = '<span class="label label-danger label-mini"><i class="fa fa-times"></i></span>' : $row[] = '<span class="label label-success label-mini"><i class="fa fa-check"></i></span>';
            $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("language_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
            $data['output']['aaData'][] = $row ;
        }
        return $data;
    }

    /**
     * Creates a new Customer entity.
     *
     * @Route("/", name="customer_create")
     * @Method("POST")
     * @Template("AppECommerceBundle:Customer:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $generator = new SecureRandom();
            $random = $generator->nextBytes(10);

            $entity->getUser()->setPassword($random);
            $entity->getUser()->setPlainPassword($random);
            $entity->getUser()->setFirstconnexion(0);
            $entity->getUser()->setRoles(array('ROLE_CUSTOMER'));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customer'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Customer entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Customer $entity)
    {
        $form = $this->createForm(new CustomerType(), $entity, array(
            'action' => $this->generateUrl('customer_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/new", name="customer_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Customer();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Customer entity.
     *
     * @Route("/{id}", name="customer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Customer entity.
     *
     * @Route("/{id}/edit", name="customer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Customer entity.
    *
    * @param Customer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Customer $entity)
    {
        $form = $this->createForm(new CustomerType(), $entity, array(
            'action' => $this->generateUrl('customer_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Customer entity.
     *
     * @Route("/{id}", name="customer_update")
     * @Method("PUT")
     * @Template("AppECommerceBundle:Customer:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customer_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Customer entity.
     *
     * @Route("/{id}", name="customer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:Customer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Customer entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customer'));
    }

    /**
     * Creates a form to delete a Customer entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
