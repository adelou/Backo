<?php

namespace App\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AdminBundle\Entity\FileManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Category controller.
 *
 * @Route("/filemanager")
 */
class FileManagerController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="filemanager")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppAdminBundle:FileManager')->findAll();
        if(empty($entities)) {

            $this->indexation($this->container->getParameter('filemanager_path'));
        }

        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>'
        );
        $htmlTree = $em->getRepository('AppAdminBundle:FileManager')->childrenHierarchy(
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
     * @Route("/", name="filemanager_save")
     * @Method("POST")
     * @Template()
     */
    public function saveAction(Request $request) {
        $filesystem = new Filesystem();
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id','');
            $name = $request->request->get('name','');
            $action = $request->request->get('action','');
            $parentid = $request->request->get('parentid','');
            $position = $request->request->get('position','');
            $old_position = $request->request->get('old_position','');
            $filemanager = null;

            switch ($action) {
                case "rename" :
                    $filemanager = $em->getRepository('AppAdminBundle:FileManager')->find($id);
                    if($filemanager) {
                        $filesystem->rename($filemanager->getPath().'/'.$filemanager->getTitle(), $filemanager->getPath().'/'.$name);
                        $filemanager->setTitle($name);
                        $em->persist($filemanager);
                        $em->flush();
                    }
                    break;
                case "delete" :
                    $filemanager = $em->getRepository('AppAdminBundle:FileManager')->find($id);
                    $filesystem->remove($filemanager->getPath().'/'.$filemanager->getTitle());
                    $em->remove($filemanager);
                    $em->flush();
                    break;
                case "move" :
                    $filemanager = $em->getRepository('AppAdminBundle:FileManager')->find($id);
                    $parentFilemanager = $em->getRepository('AppAdminBundle:FileManager')->find($parentid);
                    $filesystem->copy($filemanager->getPath().'/'.$filemanager->getTitle(), $parentFilemanager->getPath().'/'.$parentFilemanager->getTitle().'/'.$filemanager->getTitle(), $override = false);
                    //$filesystem->remove($filemanager->getPath().'/'.$filemanager->getTitle());
                    $filemanager->setParent($parentFilemanager);
                    $em->persist($filemanager);
                    $em->flush();
                    $move = (int) $position - (int) $old_position;
                    if($move < 0){
                        $move =  abs($move);
                        $em->getRepository('AppAdminBundle:FileManager')->moveUp($filemanager, $move);
                    } else if($move >0) {
                        $em->getRepository('AppAdminBundle:FileManager')->moveDown($filemanager, $move);
                    }
                    break;
            }

            if($filemanager) {
                $id = $filemanager->getId();
            } else {
                $id = null;
            }

            $response = new JsonResponse();
            $response->setData(array('id' => $id));
            return $response;
        }
    }

    public function indexation($directory, $parent = 0) {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository('AppAdminBundle:FileManager')->find($parent);
        if(is_dir($directory)){
            $iterator = new \DirectoryIterator($directory);
            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    if($fileinfo->isFile()) {
                        $file = new FileManager();
                        $file->setTitle($fileinfo->getFileName());
                        $file->setPath($iterator->getPath());
                        $file->setParent($parent);
                        $em->persist($file);
                        $em->flush();
                    } else if ($fileinfo->isDir()) {
                        $dir = new FileManager();
                        $dir->setTitle($fileinfo->getFileName());
                        $dir->setPath($iterator->getPath());
                        $dir->setParent($parent);
                        $em->persist($dir);
                        $em->flush();
                        $this->indexation($directory.'/'.$fileinfo->getFileName(), $dir->getId());
                    }
                }
            }
        }
    }
}


