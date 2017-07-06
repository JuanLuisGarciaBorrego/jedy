<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ConfigurationForm;
use AppBundle\Form\ProfileForm;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/admin_home.html.twig', [
            'count' => [
                'category' => $this->getDoctrine()->getRepository('AppBundle:Category')->getTotalCategories($this->getParameter('locale_active')),
                'post' => $this->getDoctrine()->getRepository('AppBundle:Content')->getTotalRegisters($this->getParameter('locale_active'), 'post', true),
                'page' => $this->getDoctrine()->getRepository('AppBundle:Content')->getTotalRegisters($this->getParameter('locale_active'), 'page', true),
            ],
        ]);
    }

    /**
     * @Route("/profile/", name="admin_edit_profile")
     * @Method({"GET", "POST"})
     */
    public function editProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profile = $em->getRepository('AppBundle:Profile')->find($user);
        
        $form = $this->createForm(ProfileForm::class, $profile);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['photo']->getData();
            $profile = $this->uploadPhotoProfile($file, $profile, $em);

            $em->persist($profile);
            $em->flush();
            return $this->redirectToRoute('admin_home');
        }

        return $this->render(
            'admin/admin_edit_profile.html.twig',
            [
                'form' => $form->createView(),
                'profile' => $profile
            ]
        );
    }

    /**
     * @Route("/configuration/", name="admin_edit_configuration")
     * @Method({"GET", "POST"})
     */
    public function editConfigurationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('AppBundle:Configuration')->findOneBy([]);
        
        $form = $this->createForm(ConfigurationForm::class, $config);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();
            return $this->redirectToRoute('admin_home');
        }

        return $this->render(
            'admin/admin_edit_config.html.twig',
            [
                'form' => $form->createView(),
                'config' => $config
            ]
        );
    }

    /*
    * Upload photo of profile in the entity Profile
    * @param $file Symfony\Component\HttpFoundation\File\File 
    * @param $profile AppBundle\Entity\Profile
    * @param $em Doctrine\ORM\EntityManager
    */
    private function uploadPhotoProfile($file, $profile, $em) 
    {   
        if(isset($file)) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
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
