<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Category;

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
        return $this->render('public/blog/blog_post.html.twig', ['post' => $post]);
    }
}
