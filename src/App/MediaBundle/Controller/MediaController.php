<?php

namespace App\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\MediaBundle\Entity\Media;
use App\MediaBundle\Form\Type\MediaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{

    /**
     * Lists all Media entities.
     *
     * @Route("/", name="media")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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
            $columns = array('0' => 'id', '1' => 'type', '2' => 'name');

            /* DatatableValuesArray*/
            $data = $this->container->get('app.adminbundle.services.admin')->getDatatableValuesArray($parameters, $columns, 'AppMediaBundle:Media');
            $data = $this->parseDatatableResult($data);

            /* Response */
            $response = new JsonResponse();
            $response->setData($data['output']);

            return $response;
        }
    }

    protected function parseDatatableResult($data) {

        foreach ($data['result'] as $e) {
            $row = array();
            $row[] = (string) $e->getId();
            $row[] = (string) $e->getType();
            $row[] = (string) $e->getName();
            $row[] = $this->container->get('app.media.twig')->formatImage($e, 'thumb', 50);
            $row[] = '<a class="btn btn-primary btn-sm" href="'.$this->generateUrl("media_edit", array('id' => $e->getId())).'"><i class="fa fa-crop"></i></a>
                          <a class="btn btn-danger btn-sm" onclick="confirmbox()"><i class="fa fa-trash-o "></i></a>';
            $data['output']['aaData'][] = $row ;
        }
        return $data;
    }

    /**
     * Creates a new Media entity.
     *
     * @Route("/", name="media_create")
     * @Method("POST")
     * @Template("AppMediaBundle:Media:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $filesystem = new Filesystem();
        //var_dump($this->get('kernel')->getRootDir() . '/../web/uploads/medias');die;
        if($filesystem->exists($this->get('kernel')->getRootDir() . '/../web/uploads') === false) {
            try {
                $filesystem->mkdir($this->get('kernel')->getRootDir() . '/../web/uploads', 0700);
            } catch (IOException $e) {
                echo "An error occured while creating your directory";
            }
        }
        if($filesystem->exists($this->get('kernel')->getRootDir() . '/../web/uploads/medias') === false) {
            try {
                $filesystem->mkdir($this->get('kernel')->getRootDir() . '/../web/uploads/medias', 0700);
            } catch (IOException $e) {
                echo "An error occured while creating your directory";
            }
        }

        $files = $request->files;

        $media = new Media();
        foreach ($files as $uploadedFile) {
            $media->file = $uploadedFile;
            $em->persist($media);
        }
        $em->flush();

        $src = $media->getAbsolutePath();
        $extension = $media->getExtension();
        $aDataImageOrigin = getimagesize($src);
        if (strpos($aDataImageOrigin['mime'],'image') !== false) {
            if($aDataImageOrigin[0] > $aDataImageOrigin[1]) {
                $newWidth = 50;
                $newHeight = round(50 / $aDataImageOrigin[0] * $aDataImageOrigin[1]);
            } else {
                $newWidth = round(50 / $aDataImageOrigin[1] * $aDataImageOrigin[0]);
                $newHeight = 50;
            }

            $parameters = array(
                'x' => 0, 'y' => 0,
                'w' => $aDataImageOrigin[0], 'h' => $aDataImageOrigin[1],
                'w_new' => $newWidth, 'h_new' => $newHeight,
                'src' => $src, 'slug' => "thumb", 'quality' => 100,
                'extension' => $extension,  'ajax' => false, 'path' => $media->getPath()
            );

            $this->container->get('app.media.services.media')->getCrop($parameters);
        }

        $output = array();
        $output['id'] = $media->getId();
        $output['name'] = $media->getName();
        $output['path'] = $media->getPath();

        $response = new JsonResponse();
        $response->setData($output);

        return $response;

    }

    /**
     * Creates a form to create a Media entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Media $entity)
    {
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Displays a form to create a new Media entity.
     *
     * @Route("/new", name="media_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Media();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/{id}/edit", name="media_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
            
        $formats = $em->getRepository('AppMediaBundle:Croping')->findAll();
        
        // Recuperation des metadonnées de l'image.
        $filename = 'uploads/medias/'.$entity->getPath();
        
        $aDataImageOrigin = getimagesize($filename);
        
        $iCoeffCrop = $aDataImageOrigin[0] / 500;
        $iImageWidth =  $aDataImageOrigin[0];

        if ( $aDataImageOrigin[0] > 500) {
            $iImageWidth = 500;
        }

        return array(
            'iImageWith'  => $iImageWidth,
            'dCoeffCrop'  => $iCoeffCrop,
            'entity'      => $entity,
            'formats'     => $formats,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Media entity.
    *
    * @param Media $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Media $entity)
    {
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('media_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Media entity.
     *
     * @Route("/{id}", name="media_update")
     * @Method("PUT")
     * @Template("AppMediaBundle:Media:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $entity->upload();
        $em->persist($entity);
        $em->flush();

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Media entity.
     *
     * @Route("/{id}", name="media_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppMediaBundle:Media')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Media entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('media'));
    }

    /**
     * Creates a form to delete a Media entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('media_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Supprimer',
                'attr'  => array(
                    'class' => 'btn btn-xs btn-danger'
                )))
            ->getForm()
        ;
    }
    
    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/crop", name="media_crop")
     * @Method("POST")
     * @Template()
     */
    public function cropAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $parameters = $request->request->get('parameters');
            $this->container->get('app.media.services.media')->getCrop($parameters);
        }

        $response = new JsonResponse();
        return $response;

    }

    /**
     * Delete langue entity.
     *
     * @Route("/deletemedia/{id}", name="delete_media_datatable")
     */
    public function deleteMediaAction($id)
    {
        $oEm = $this->getDoctrine()->getManager();
        $oMediaSelected = $oEm->getRepository('AppMediaBundle:Media')->find($id);


        if (!$oMediaSelected) {
            throw $this->createNotFoundException('Unable to find Field entity.');
        }


        $oEm->remove($oMediaSelected);
        $oEm->flush();

        return $this->redirect($this->generateUrl('media'));
    }

    /**
     * Fonctionne avec le bouton de l'interface Product:new
     *
     * @Route("/formupdate", name="form_media_update")
     * @Template()
     */
    public function addMediaFromUpdateAction()
    {
        $entity = new Media();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );

    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/ajax", name="media_ajax")
     * @Method("GET")
     * @Template()
     */
    public function ajaxAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        if($request->isXmlHttpRequest()) {

            $type = $request->get('type');
            $search = $request->get('term');

            $qb = $em->getRepository('AppMediaBundle:Media')->createQueryBuilder('m');
            $qb->where($qb->expr()->like('LOWER(m.type)',  $qb->expr()->literal('%'.$type.'%')));
            $orX = $qb->expr()->orX();
            $orX->add($qb->expr()->like('LOWER(m.name)',  $qb->expr()->literal('%'.$search.'%')));
            $orX->add($qb->expr()->like('LOWER(m.id)',  $qb->expr()->literal('%'.$search.'%')));
            $qb->andWhere($orX);
            $result =  $qb->getQuery()->getResult();

            $data = array();
            foreach($result as $key => $res) {
                $tmp = array();
                $tmp['value'] = $res->getId();
                $tmp['type'] = $res->getType();
                $tmp['label'] = $res->getName();;
                $tmp['img'] = $this->container->get('app.media.twig')->formatImage($res, 'thumb', 50);
                $data[$key] = $tmp;
            }

            $response = new JsonResponse();
            $response->setData($data);

            return $response;

        }
    }

    /**
     * Jquery Sortable to change media position
     *
     * @Route("/media-order", name="media_order")
     * @Method("POST")
     * @Template()
     */
    public function mediaOrderAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        if($request->isXmlHttpRequest()) {

            $medias_id = $request->request->get('medias_id');
            $repo = $request->request->get('repo');
            $object_id = $request->request->get('id');
            //$object = $request->request->get('object');

            foreach($medias_id as $key => $id) {
                $entity = $em->getRepository($repo)->findOneBy(array('article' => $object_id, 'media' => $id));
                $currentPos = $entity->getPosition();
                if($currentPos != $key) {
                    $entity->setPosition($key);
                    $em->persist($entity);
                }
            }
            $em->flush();

            $response = new JsonResponse();
            $response->setData();

            return $response;
        }

    }


}
