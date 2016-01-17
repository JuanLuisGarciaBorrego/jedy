<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Nav;
use AppBundle\Form\NavContents\NavCategoryForm;
use AppBundle\Form\NavForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/admin/nav")
 */
class NavController extends Controller
{
    /**
     * @Route("s/", name="admin_nav_home")
     */
    public function indexAction()
    {
        return $this->render('admin/nav/admin_nav_index.html.twig', [
            'navs' => $this->getDoctrine()->getRepository('AppBundle:Nav')->findBy(['locale' => $this->get('locales')->getLocaleActive()])
        ]);
    }

    /**
     * @Route("/new/", name="admin_nav_new")
     */
    public function newAction(Request $request)
    {
        $nav = new Nav($this->get('locales')->getLocaleActive());

        $form = $this->createForm(NavForm::class, $nav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($nav);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_nav_home');
        }

        return $this->render(
            'admin/nav/admin_nav_new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/add-content/", name="admin_nav_add_content")
     */
    public function addContentToNavAction(Nav $nav, Request $request)
    {
        $contents = $request->getSession()->has('contents') ? $request->getSession()->get('contents') : new ArrayCollection();

        $formCategory = $this->createForm(NavCategoryForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);
        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()) {

            $category = $formCategory->getData();

            $item = [
                'parent_id' => null,
                'nav_id' => $nav->getId(),
                'idElement' => $category['category']->getId(),
                'sort' => null,
                'name' => $category['category']->getName(),
                'slug' => $category['category']->getSlug(),
                'type' => 'category'
            ];

            if(!$contents->contains($item)) {
                $contents->add($item);
                $request->getSession()->set('contents', $contents);
            }else{
                $this->addFlash('error', 'exits');
            }

        }

        return $this->render(
            'admin/nav/admin_nav_add_content.html.twig', [
                'nav' => $nav,
                'form_category' => $formCategory->createView(),
                'contents' => $request->getSession()->get('contents')
            ]
        );
    }

    /**
     * @Route("/{id}/remove-content-nav/{keyArray}", name="admin_nav_remove_content")
     */
    public function removeContentNavAction($id, $keyArray)
    {
        $contents = $this->get('session')->get('contents');
        $contents->remove($keyArray);

        return $this->redirectToRoute('admin_nav_add_content', ['id' => $id]);
    }
}
