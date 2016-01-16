<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Nav;
use AppBundle\Form\NavForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/nav")
 */
class NavController extends Controller
{
    /**
     * @Route("s/", name="admin_nav_home")
     */
    public function indexAction()
    {
        return $this->render('admin/nav/admin_nav_index.html.twig', [
            'navs' => $this->getDoctrine()->getRepository('AppBundle:Nav')->findBy(['locale' => $this->get('locales')->getLocaleActive()])
        ]);
    }

    /**
     * @Route("/new/", name="admin_nav_new")
     */
    public function newAction(Request $request)
    {
        $nav = new Nav($this->get('locales')->getLocaleActive());

        $form = $this->createForm(NavForm::class, $nav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($nav);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_nav_home');
        }

        return $this->render(
            'admin/category/admin_category_new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
