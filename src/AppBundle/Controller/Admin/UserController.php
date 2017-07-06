<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\UserForm;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\ProfileForm;
use AppBundle\Entity\Profile;

/**
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * @Route("s/", name="admin_user_home")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render(
            'admin/user/admin_user_index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * @Route("/new/", name="admin_user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.flash.created');

            return $this->redirectToRoute('admin_user_home');
        }

        return $this->render(
            'admin/user/admin_user_new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("{id}/edit/", name="admin_user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        $form_delete = $this->formDelete($user);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.flash.edited');

            return $this->redirectToRoute('admin_user_home');
        }

        return $this->render(
            'admin/user/admin_user_edit.html.twig',
            [
                'form' => $form->createView(),
                'form_delete' => $form_delete->createView(),
            ]
        );
    }

    /**
     * @Route("{id}/delete/", name="admin_user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(User $user, Request $request)
    {
        $form = $this->formDelete($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            try {
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('success', 'user.flash.deleted');

                return $this->redirectToRoute('admin_user_home');
            }

            $this->addFlash('success', 'user.flash.deleted');
        }

        return $this->redirectToRoute('admin_user_home');
    }

    /**
     * @param user $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function formDelete(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', ['id' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/profile/", name="admin_edit_profile")
     * @Method({"GET", "POST"})
     */
    public function editProfileAction(Request $request)
    {

        $form = $this->createForm(ProfileForm::class, $this->getUser()->getProfile());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['photo']->getData();
            $profile = $this->uploadPhotoProfile($file, $this->getUser()->getProfile());

            $em = $this->getDoctrine()->getManager();

            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', 'profile.flash.edited');

            return $this->redirectToRoute('admin_edit_profile');
        }

        return $this->render(
            'admin/admin_edit_profile.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /*
    * Upload photo of profile in the entity Profile
    * @param $file Symfony\Component\HttpFoundation\File\File
    * @param $profile AppBundle\Entity\Profile
    * @param $em Doctrine\ORM\EntityManager
    */
    private function uploadPhotoProfile($file, $profile)
    {
        $em = $this->getDoctrine()->getManager();

        if (isset($file)) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('profile_directory'), $fileName);
            $profile->setPhoto($fileName);
        } else {
            $original_data = $em->getUnitOfWork()->getOriginalEntityData($profile);
            $photo = $original_data['photo'];
            $profile->setPhoto($photo);
        }

        return $profile;
    }
}
