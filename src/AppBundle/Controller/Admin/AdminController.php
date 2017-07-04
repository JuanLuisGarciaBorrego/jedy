<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ProfileForm;
use AppBundle\Entity\Profile;

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
            if(isset($file)) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('profile_directory'), $fileName);
                $profile->setPhoto($fileName);
            } else {
                $original_data = $em->getUnitOfWork()->getOriginalEntityData($profile);
                $photo = $original_data['photo'];
                $profile->setPhoto($photo);
            }

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
}
