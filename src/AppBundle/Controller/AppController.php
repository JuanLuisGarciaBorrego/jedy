<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use AppBundle\Entity\ContentsNav;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{
    /**
     * @Route("/", name="app_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('AppBundle:Configuration')->findOneBy([]);
        return $this->render('public/app/app_index.html.twig', ['config' => $config]);
    }

    /**
     * @Route("/{slug}", name="app_page")
     * @Method("GET")
     */
    public function pageAction(Content $content)
    {
        return $this->render('public/app/app_page.html.twig', ['page' => $content]);
    }

    public function navAction($locale)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('AppBundle:Configuration')->findOneBy([]);
        $contentsNav = $em->getRepository('AppBundle:ContentsNav')->getNav($locale);
        return $this->render('public/app/app_nav.html.twig', ['nav' => $contentsNav]);
    }
}
