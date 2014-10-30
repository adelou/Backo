<?php

namespace App\ECommerceBundle\Controller\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\ECommerceBundle\Entity\Product\Product;
use App\ECommerceBundle\Form\Product\ProductType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Product\Product controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller
{

    /**
     * Lists all Product\Product entities.
     *
     * @Route("/", name="product")
     * @Method("GET")
     * @Template()
     * @Secure(roles="PRODUCT_VIEW, ROLE_SUPER_ADMIN")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {

            $qb = $em->getRepository('AppECommerceBundle:Product\Product')->createQueryBuilder('a');
            $qb->select('a');

            $filters = $request->get('filters');
            $lang = $request->get('lang');
            if(empty($lang)){
                $lang = $this->container->getParameter('locale');
            }
            if(!empty($filters)) {
                (isset($filters['enabled'])) ? $qb->where("a.enabled = 1") : $qb->where("a.enabled = 0");
               /* $andModule = $qb->expr()->andx();
                if(isset($filters['isoCode']) && !empty($filters['isoCode'])) {
                    $andModule->add($qb->expr()->like('LOWER(a.isoCode)',  $qb->expr()->literal('%'.strtolower(addslashes($filters['isoCode'])).'%')));
                }
                $qb->andWhere($andModule);*/
            }

            $qb_count = clone $qb;
            $qb->setFirstResult($request->get('iDisplayStart'));
            $qb->setMaxResults($request->get('iDisplayLength'));
            $result =  $qb->getQuery()->getResult();

            $qb_count->select('COUNT(a)');
            $total =  $qb_count->getQuery()->getSingleScalarResult();

            $output = array(
                "sEcho" => intval($request->get('sEcho')),
                "iTotalRecords" => intval($total),
                "iTotalDisplayRecords" => intval($total),
                "aaData" => array()
            );

            foreach ($result as $e) {
                $row   = array();
                $row[] = (string) $e->getId();
                $row[] = (string) $e->getPosition();
                $row[] = (string) $e->getName();
                $row[] = "";//TODO
                $row[] = "";//TODO
                $row[] = "";//TODO
                $row[] = "";//TODO SLUG
                $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("product_edit", array('id' => $e->getId())).'"><i class="fa fa-pencil"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
                $output['aaData'][] = $row ;

            }
            $response = new JsonResponse();
            $response->setData($output);

            return $response;
        }


    }
    /**
     * Creates a new Product\Product entity.
     *
     * @Route("/", name="product_create")
     * @Method("POST")
     * @Template("AppECommerceBundle:Product\Product:new.html.twig")
     * @Secure(roles="PRODUCT_CREATE, ROLE_SUPER_ADMIN")
     */
    public function createAction(Request $request)
    {
        $entity = new Product();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('product', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Product\Product entity.
     *
     * @param Product $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Product $entity)
    {
        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('product_create'),
            'method' => 'POST',
            'em' => $this->getDoctrine()->getManager()
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Product\Product entity.
     *
     * @Route("/new", name="product_new")
     * @Method("GET")
     * @Template()
     * @Secure(roles="PRODUCT_CREATE, ROLE_SUPER_ADMIN")
     */
    public function newAction()
    {
        $entity = new Product();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product\Product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method("GET")
     * @Template()
     * @Secure(roles="PRODUCT_EDIT, ROLE_SUPER_ADMIN")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Product\Product')->find($id);
        $lang = $request->query->get('lang');
        if(empty($lang)) {$lang = $request->getLocale();}
        $entity->setTranslatableLocale($lang);
        $em->refresh($entity);
        //$medias = $em->getRepository('AppMediaBundle:Media')->findBy(array('products' => $entity));

        $qb = $em->getRepository('AppMediaBundle:Media')->createQueryBuilder('m');
        $qb->select('m');
        $qb->leftJoin('m.products','p');
        $qb->where('p.id = '.$entity->getId());
        $medias = $qb->getQuery()->getResult();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Product entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'medias'      => $medias,
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Product\Product entity.
    *
    * @param Product $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Product $entity)
    {
        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'em' => $this->getDoctrine()->getManager()
        ));

        return $form;
    }
    /**
     * Edits an existing Product\Product entity.
     *
     * @Route("/{id}/{lang}", name="product_update")
     * @Method("PUT")
     * @Template("AppECommerceBundle:Product\Product:edit.html.twig")
     * @Secure(roles="PRODUCT_EDIT, ROLE_SUPER_ADMIN")
     */
    public function updateAction(Request $request, $id, $lang = "")
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppECommerceBundle:Product\Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Product entity.');
        }

        $originalMedias = new ArrayCollection();
        foreach ($entity->getMedias() as $media) {
            $originalMedias->add($media);
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        
        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                
                foreach ($originalMedias as $media) {   
                    if (false === $entity->getMedias()->contains($media)) {
                        $media->getProducts()->removeElement($entity);
                        $em->persist($media);
                    }
                }

                if(!empty($lang)) {
                    $entity->setTranslatableLocale($lang);
                }

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('product_edit', array('id' => $id, 'lang' => $lang)));
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Product\Product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppECommerceBundle:Product\Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product\Product entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * Creates a form to delete a Product\Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
