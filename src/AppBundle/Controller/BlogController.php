<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Category;

/**
 * @Route("/blog")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="app_blog_index", defaults={"page" = 1})
     * @Route("/page/{page}", name="app_blog_index_paginated", requirements={"page" : "\d+"})
     */
    public function indexAction($page)
    {
        $postPagination = $this->get('pagination')->pagination(
            'post',
            $page,
            $this->get('request_stack')->getMasterRequest()->get('_locale'),
            true
        );

        return $this->render(
            'blog/blog_index.html.twig',
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
     * @Route("/post/{slug}", name="app_blog_post")
     * @ParamConverter("post", class="AppBundle:Content", options={"repository_method" = "findBySlugIfContentIsPublished"})
     */
    public function postShowAction(Content $post)
    {
        return $this->render('blog/blog_post.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/category/{slug}", name="app_blog_category")
     */
    public function categoryAction(Category $category)
    {
        return $this->render('blog/blog_category.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/page/{slug}", name="app_blog_page")
     */
    public function pageAction(Content $content)
    {
        return $this->render('blog/blog_page.html.twig', ['page' => $content]);
    }
}
