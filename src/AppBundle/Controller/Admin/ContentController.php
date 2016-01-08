<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin/content")
 */
class ContentController extends Controller
{
    /**
     * @Route("s/", name="admin_content_home")
     */
    public function indexAction()
    {
        $contents = $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(
            ['locale' => $this->container->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/content/admin_content_index.html.twig',
            [
                'contents' => $contents,
            ]
        );
    }
}