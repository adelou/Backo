<?php

namespace App\LanguageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\LanguageBundle\Entity\Country;
use App\LanguageBundle\Form\CountryType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Country controller.
 *
 * @Route("/country")
 */
class CountryController extends Controller
{

    /**
     * Lists all Country entities.
     *
     * @Route("/", name="country")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {

            /* DataTable Parameters*/
            $filters = $request->get('filters');
            $lang = $request->get('lang');
            if(empty($lang)){$lang = $this->container->getParameter('locale');}
            $sortCol = $request->get('iSortCol_0');
            $sortDir = $request->get('iSortDir_0');
            $start = $request->get('iDisplayStart');
            $limit = $request->get('iDisplayLength');

            /* Columns */
            $columns = array();
            $columns[0] = 'id';
            $columns[1] = 'position';
            $columns[2] = 'isoCode';
            $columns[3] = 'isoCode';
            $columns[4] = 'enabled';

            /* Query Result */
            $qb = $em->getRepository('AppLanguageBundle:Country')->createQueryBuilder('a');
            $qb->select('a');
            if(!empty($filters)) {
                (isset($filters[$columns[4]])) ? $qb->where('a.'.$columns[4].' = 1') : $qb->where('a.'.$columns[4].' = 0');
                $andModule = $qb->expr()->andx();
                if(isset($filters[$columns[2]]) && !empty($filters[$columns[2]])) {
                    $andModule->add($qb->expr()->like('LOWER(a.'.$filters[$columns[2]].')',  $qb->expr()->literal('%'.strtolower(addslashes($filters[$columns[2]])).'%')));
                }
                $qb->andWhere($andModule);
            }
            $qb_count = clone $qb;
            $qb->setFirstResult($start);
            $qb->setMaxResults($limit);
            $qb->orderBy('a.'.$columns[$sortCol], $sortDir);
            $result =  $qb->getQuery()->getResult();

            /* Query Count */
            $qb_count->select('COUNT(a)');
            $total =  $qb_count->getQuery()->getSingleScalarResult();

            $output = array(
                "sEcho" => intval($request->get('sEcho')),
                "iTotalRecords" => intval($total),
                "iTotalDisplayRecords" => intval($total),
                "aaData" => array()
            );

            /* Parse Result */
            foreach ($result as $e) {
                $row = array();
                $row[] = (string) $e->getId();
                $row[] = (string) $e->getPosition();
                $row[] = (string) $this->container->get('app.language.twig')->country($e->getIsoCode(), $lang);
                $row[] = (string) $e->getIsoCode();
                ($e->getEnabled()==0) ? $row[] = '<span class="label label-danger label-mini"><i class="fa fa-times"></i></span>' : $row[] = '<span class="label label-success label-mini"><i class="fa fa-check"></i></span>';
                $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("country_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
                $output['aaData'][] = $row ;

            }
            $response = new JsonResponse();
            $response->setData($output);

            return $response;
        }
    }
    /**
     * Creates a new Country entity.
     *
     * @Route("/", name="country_create")
     * @Method("POST")
     * @Template("AppLanguageBundle:Country:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Country();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('country'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Country entity.
     *
     * @param Country $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Country $entity)
    {
        $form = $this->createForm(new CountryType(), $entity, array(
            'action' => $this->generateUrl('country_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Country entity.
     *
     * @Route("/new", name="country_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Country();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Country entity.
     *
     * @Route("/{id}", name="country_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Country entity.
     *
     * @Route("/{id}/edit", name="country_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
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
    * Creates a form to edit a Country entity.
    *
    * @param Country $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Country $entity)
    {
        $form = $this->createForm(new CountryType(), $entity, array(
            'action' => $this->generateUrl('country_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Country entity.
     *
     * @Route("/{id}", name="country_update")
     * @Method("PUT")
     * @Template("AppLanguageBundle:Country:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLanguageBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('country'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Country entity.
     *
     * @Route("/{id}", name="country_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppLanguageBundle:Country')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Country entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('country'));
    }

    /**
     * Creates a form to delete a Country entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('country_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
