<?php

namespace App\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\UserType;
use Symfony\Component\Form\FormError;

/**
 * User controller.
 *
 * @Route("/user", name="user_list")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_list")
     * @Method({"GET", "PUT"})
     * @Template("AppUserBundle:User:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppUserBundle:User')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("AppUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $data = $request->get($form->getName(), array());
        $user  = $em->getRepository('AppUserBundle:User')->findOneBy(array('email' => $data["email"]));
        if ($user instanceof \App\UserBundle\Entity\User) {
            $form->get('email')->addError(new FormError('Cet email est déjà existant'));
        }
        $user  = $em->getRepository('AppUserBundle:User')->findOneBy(array('username' => $data["username"]));
        if ($user instanceof \App\UserBundle\Entity\User) {
            $form->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà existant'));
        }

        if ($form->isValid()) {
            $entity->setUsernameCanonical($data["username"]);
            $entity->setEmailCanonical($data["email"]);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_list'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $roles = $this->get('app.userbundle.services.roles')->getRoles();
        $form = $this->createForm(new UserType($roles), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template("AppUserBundle:User:new.html.twig")
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);
        
        return array(
            'entity' => $entity,
            'roles' => $entity->getRoles(),
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show2")
     * @Method("GET")
     * @Template("AppUserBundle:User:show.html.twig")
     */
    public function showAction($id = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET","POST"})
     * @Template("AppUserBundle:User:edit.html.twig")
     */
    public function editAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'roles' => $entity->getRoles(),
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $roles = $this->get('app.userbundle.services.roles')->getRoles();
        $form = $this->createForm(new UserType($roles), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     * @Template("AppUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:User')->find($id);
        $username = $entity->getUsername();
        $email = $entity->getEmail();
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $data = $request->get($editForm->getName(), array());
        $user  = $em->getRepository('AppUserBundle:User')->findOneBy(array('email' => $data["email"]));
        if ($user instanceof \App\UserBundle\Entity\User && $data["email"] != $email) {
            $editForm->get('email')->addError(new FormError('Cet email est déjà existant'));
        }
        $user  = $em->getRepository('AppUserBundle:User')->findOneBy(array('username' => $data["username"]));
        if ($user instanceof \App\UserBundle\Entity\User && $data["username"] != $username) {
            $editForm->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà existant'));
        }
        if(!empty($data["plainPassword"]['first']) && !empty($data["plainPassword"]['second']) && $data["plainPassword"]['second'] == $data["plainPassword"]['first']) {
            $userManager = $this->container->get('fos_user.user_manager');
            $user->setPassword($data["plainPassword"]['first']);
            $userManager->updateUser($user);
        }

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('user_list'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('user_list'));
    }


}
