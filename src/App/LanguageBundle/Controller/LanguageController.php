<?php

namespace App\LanguageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\LanguageBundle\Entity\Language;
use App\LanguageBundle\Form\Type\LanguageType;
use App\LanguageBundle\Form\Type\LanguageFilterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;

/**
 * Language controller.
 *
 * @Route("/language")
 */
class LanguageController extends Controller
{

    /**
     * Lists all Language entities.
     * @Method({"GET", "POST"})
     * @Route("/", name="language")
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
            $columns = array('0' => 'id', '1' => 'position', '2' => 'isoCode', '3' => 'isoCode', '4' => 'enabled');

            /* DatatableValuesArray*/
            $data = $this->container->get('app.adminbundle.services.admin')->getDatatableValuesArray($parameters, $columns, 'AppLanguageBundle:Language');
            $data = $this->parseDatatableResult($data, $parameters);

            /* Response */
            $response = new JsonResponse();
            $response->setData($data['output']);

            return $response;
        }
    }

    protected function parseDatatableResult($data, $parameters) {

        foreach ($data['result'] as $e) {
            $row = array();
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

    public function filterEntitiesAction($filter)
    {
        $em        = $this->getDoctrine()->getManager();
        $query	= $em->getRepository("AppLanguageBundle:Language")->createQueryBuilder('a');
        if(isset($filter['enabled'])) {
            $query->where("a.enabled = 1");
        } else {
            $query->where("a.enabled = 0");
        }
        $andModule = $query->expr()->andx();
        if(isset($filter['isoCode']) && !empty($filter['isoCode'])) {
            $andModule->add($query->expr()->like('LOWER(a.isoCode)',  $query->expr()->literal('%'.strtolower(addslashes($filter['isoCode'])).'%')));
        }
        $query->andWhere($andModule);

        return $query->getQuery()->getResult();
    }

    /**
     * Displays a form to create a new Filter Language entity.
     *
     * @Route("/filter", name="language_filter")
     * @Method("POST")
     * @Template()
     */
    public function filterAction(Request $request)
    {
        $entity = new Language();
        $form   = $this->createFilterForm($entity, $request);
        $form->handleRequest($request);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Language entity.
     *
     * @param Language $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createFilterForm(Language $entity, Request $request)
    {
        $form = $this->createForm(new LanguageFilterType(), $entity, array(
            'action' => $this->generateUrl('language',array('lang'=>$request->get('lang'))),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Rechercher'));

        return $form;
    }

    /**
     * Creates a new Language entity.
     *
     * @Route("/create", name="language_create")
     * @Method("POST")
     * @Template("AppLanguageBundle:Language:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Language();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('language'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Language entity.
     *
     * @param Language $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Language $entity)
    {
        $form = $this->createForm(new LanguageType(), $entity, array(
            'action' => $this->generateUrl('language_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Language entity.
     *
     * @Route("/new", name="language_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Language();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Language entity.
     *
     * @Route("/{id}", name="language_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Language entity.
     *
     * @Route("/{id}/edit", name="language_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
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
    * Creates a form to edit a Language entity.
    *
    * @param Language $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Language $entity)
    {
        $form = $this->createForm(new LanguageType(), $entity, array(
            'action' => $this->generateUrl('language_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Language entity.
     *
     * @Route("/{id}", name="language_update")
     * @Method("PUT")
     * @Template("AppLanguageBundle:Language:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Language')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Language entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('language'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Language entity.
     *
     * @Route("/{id}", name="language_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppLanguageBundle:Language')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Language entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('language'));
    }

    /**
     * Creates a form to delete a Language entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('language_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Lists all Language entities.
     *
     * @Route("/list", name="language_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction($lang, $routing, $id = "")
    {
        $em = $this->getDoctrine()->getManager();
        if(empty($lang)) {
           $lang =  $this->container->getParameter('locale');
        }

        $languages = $em->getRepository('AppLanguageBundle:Language')->findAll();

        return array(
            'lang' => $lang,
            'languages' => $languages,
            'routing' => $routing,
            'id' => $id
        );
    }
}
