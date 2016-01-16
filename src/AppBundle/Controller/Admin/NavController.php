<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/admin/nav")
 */
class NavController extends Controller
{
    /**
     * @Route("/", name="admin_nav_home")
     */
    public function indexAction()
    {
        return $this->render('admin/nav/admin_nav_index.html.twig', [
            'navs' => $this->getDoctrine()->getRepository('AppBundle:Nav')->findBy(['locale' => $this->get('locales')->getLocaleActive()])
        ]);
    }
}
