<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin/categor")
 */
class CategoryController extends Controller
{
    /**
     * @Route("ies/", name="admin_category_home")
     */
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(
            ['locale' => $this->container->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/category/admin_category_index.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }
}