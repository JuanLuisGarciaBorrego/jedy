<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin/post")
 */
class PostController extends Controller
{
    /**
     * @Route("s/", name="admin_home")
     */
    public function indexAction()
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(
            ['locale' => $this->container->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/post/admin_post_index.html.twig',
            [
                'posts' => $posts,
            ]
        );
    }
}