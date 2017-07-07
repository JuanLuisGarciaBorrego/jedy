<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ConfigurationForm;


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
        return $this->render(
            'admin/admin_home.html.twig',
            [
                'count' => [
                    'category' => $this->getDoctrine()->getRepository('AppBundle:Category')->getTotalCategories(
                        $this->getParameter('locale_active')
                    ),
                    'post' => $this->getDoctrine()->getRepository('AppBundle:Content')->getTotalRegisters(
                        $this->getParameter('locale_active'),
                        'post',
                        true
                    ),
                    'page' => $this->getDoctrine()->getRepository('AppBundle:Content')->getTotalRegisters(
                        $this->getParameter('locale_active'),
                        'page',
                        true
                    ),
                ],
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

            $this->addFlash('success', 'configuration.flash.edited');

            return $this->redirectToRoute('admin_edit_configuration');
        }

        return $this->render(
            'admin/admin_edit_config.html.twig',
            [
                'form' => $form->createView(),
                'config' => $config
            ]
        );
    }
}
