<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_home")
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
}
