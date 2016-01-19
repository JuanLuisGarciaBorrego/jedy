<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ContentsNav;
use AppBundle\Entity\Nav;
use AppBundle\Form\NavContents\NavCategoryForm;
use AppBundle\Form\NavContents\NavPageForm;
use AppBundle\Form\NavForm;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
        //$request->getSession()->remove('contents');
        $sessionContents = $request->getSession()->has('contents') ? $request->getSession()->get('contents') : new ArrayCollection();

        $nav = new Nav($this->get('locales')->getLocaleActive());

        foreach ($sessionContents as $item) {
            $this->createContentsNav($item, $nav);
        }

        $formCategory = $this->createForm(NavCategoryForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);
        $formPage = $this->createForm(NavPageForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);

        $formCategory->handleRequest($request);
        $formPage->handleRequest($request);

        if (($formCategory->isSubmitted() && $formCategory->isValid()) || ($formPage->isSubmitted() && $formPage->isValid())) {

            $category = $formCategory->getData();
            $page = $formPage->getData();

            //:::::::::::::::::::::::::::::::::::
            $sessionContent = [
                'idElement' => $category['category']->getId(),
                'name' => $category['category']->getName(),
                'type' => 'category',
                'sort' => 0,
                'parent' => null,
            ];

            if (!$sessionContents->contains($sessionContent)) {
                $sessionContents->add($sessionContent);
                $request->getSession()->set('contents', $sessionContents);
            } else {
                //$this->addFlash('error', 'exits');
            }

            $this->createContentsNav($sessionContent, $nav);

            //:::::::::::::::::::::::::::::::::::
        }

        $formNavContents = $this->createForm(NavForm::class, $nav, ['contentsNav' => $request->getSession()->get('contents')]);
        $formNavContents->handleRequest($request);

        if ($formNavContents->isSubmitted() && $formNavContents->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($nav);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_nav_home');
        }

        return $this->render(
            'admin/nav/admin_nav_new.html.twig',
            [
                'formNavContents' => $formNavContents->createView(),
                'form_category' => $formCategory->createView(),
                'form_page' => $formPage->createView(),
            ]
        );
    }

    private function createContentsNav($sessionContent, Nav $nav)
    {
        $contentsNav = new ContentsNav();
        $contentsNav->setIdElement($sessionContent['idElement']);
        $contentsNav->setName($sessionContent['name']);
        $contentsNav->setType($sessionContent['type']);
        $contentsNav->setSort($sessionContent['sort']);
        $contentsNav->setParent($sessionContent['parent']);
        $nav->getContentsNav()->add($contentsNav);
    }
}