<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/blog")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="app_blog_index", defaults={"page" = 1})
     * @Route("/{page}", name="app_blog_index_paginated", requirements={"page" : "\d+"})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $this->check_enable_blog();
        $postPagination = $this->get('pagination')->create(
            'post',
            $page,
            $this->get('request_stack')->getMasterRequest()->get('_locale'),
            true
        );

        return $this->render(
            'public/blog/blog_index.html.twig',
            [
                'posts' => $postPagination['contents'],
                'type' => $postPagination['type'],
                'total' => $postPagination['total'],
                'totalPages' => $postPagination['totalPages'],
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/{slug}", name="app_blog_category", defaults={"page" = 1})
     * @Route("/{slug}/{page}", name="app_blog_category_paginated", requirements={"page" : "\d+"})
     * @Method("GET")
     */
    public function categoryAction(Category $category, $page)
    {
        $this->check_enable_blog();
        $postPagination = $this->get('pagination')->create(
            'post',
            $page,
            $this->get('request_stack')->getMasterRequest()->get('_locale'),
            true,
            $category
        );

        return $this->render(
            'public/blog/blog_category.html.twig',
            [
                'category' => $category,
                'posts' => $postPagination['contents'],
                'type' => $postPagination['type'],
                'total' => $postPagination['total'],
                'totalPages' => $postPagination['totalPages'],
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/{slugcategory}/{slug}", name="app_blog_post")
     * @Method("GET")
     * @ParamConverter("post", class="AppBundle:Content", options={
     *      "repository_method" : "findBySlugIfContentIsPublished" ,
     *      "exclude": {"slugcategory"}
     * })
     */
    public function postShowAction(Content $post)
    {
        $this->check_enable_blog();
        return $this->render('public/blog/blog_post.html.twig', ['post' => $post]);
    }

    /*
    * Check if is enable the blog in the configuration
    */
    private function check_enable_blog() 
    {
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('AppBundle:Configuration')->findOneBy([]);
        if (!$config->getEnableBlog()) 
        {
            throw new NotFoundHttpException("Page not found");
        }
    }
}
