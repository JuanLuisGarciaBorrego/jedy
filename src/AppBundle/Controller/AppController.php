<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
}
