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
        $this->get('session')->remove('contents');
        $this->get('session')->remove('flagNav');

        return $this->render('admin/nav/admin_nav_index.html.twig', [
            'navs' => $this->getDoctrine()->getRepository('AppBundle:Nav')->findBy(['locale' => $this->get('locales')->getLocaleActive()])
        ]);
    }

    /**
     * @Route("/new/", name="admin_nav_new")
     * @Route("/new/{idRemove}", name="admin_nav_new_remove_element", requirements={"idRemove" : "\d+"})
     * @Route("/edit/{id}", name="admin_nav_edit")
     */
    public function newAction(Request $request, $idRemove = null, Nav $nav = null)
    {
        $sessionContents = $request->getSession()->has('contents') ? $request->getSession()->get('contents') : new ArrayCollection();

        if ($idRemove) {
            foreach ($sessionContents as $item) {
                if ($item['idElement'] == $idRemove) {
                    $sessionContents->removeElement($item);
                    return $this->redirectToRoute('admin_nav_new');
                }
            }
        }

        if (!$nav) {
            $nav = new Nav($this->get('locales')->getLocaleActive());
            foreach ($sessionContents as $item) {
                $this->createContentsNav($item, $nav);
            }

        } else {
            $request->getSession()->has('flagNav') ? $request->getSession()->get('flagNav') : $request->getSession()->set('flagNav', true);

            if ($request->getSession()->get('flagNav')) {

                foreach ($nav->getContentsNav()->toArray() as $item) {
                    $contentsNavBD = [
                        'idElement' => $item->getIdElement(),
                        'name' => $item->getName(),
                        'type' => $item->getType(),
                        'sort' => $item->getSort(),
                        'parent' => $item->getParent(),
                    ];
                    $sessionContents->add($contentsNavBD);
                }
                $request->getSession()->set('contents', $sessionContents);
                $request->getSession()->set('flagNav', false);
            }
        }

        $formCategory = $this->createForm(NavCategoryForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);
        $formPage = $this->createForm(NavPageForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);

        $formCategory->handleRequest($request);
        $formPage->handleRequest($request);

        if (($formCategory->isSubmitted() && $formCategory->isValid()) || ($formPage->isSubmitted() && $formPage->isValid())) {

            $category = $formCategory->getData();
            $page = $formPage->getData();

            if ($category) {
                $sessionContent = $this->createSession($category['category'], 'category');
            } else {
                $sessionContent = $this->createSession($page['page'], 'page');
            }

            $checkIdElement = function ($sessionContent) use ($sessionContents) {
                foreach ($sessionContents as $item) {
                    if ($item['idElement'] == $sessionContent['idElement']) {
                        return false;
                    }
                }
                return true;
            };

            if ($checkIdElement($sessionContent)) {
                $sessionContents->add($sessionContent);
                $request->getSession()->set('contents', $sessionContents);
                $contentsNavOb = $this->createContentsNav($sessionContent, $nav);

                $em = $this->getDoctrine()->getManager();
                $em->persist($contentsNavOb);
                $em->flush();

            } else {
                $this->addFlash('error', 'exits');
            }
        }

        $formNavContents = $this->createForm(NavForm::class, $nav, ['contentsNav' => $this->createArray($sessionContents)]);
        $formNavContents->handleRequest($request);

        if ($formNavContents->isSubmitted() && $formNavContents->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($nav);
            $em->flush();

            $request->getSession()->remove('contents');
            $request->getSession()->remove('flagNav');

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
        $contentsNav->setNav($nav);
        $nav->addContentsNav($contentsNav);

        return $contentsNav;

    }

    private function createSession($item, $type)
    {
        return [
            'idElement' => $item->getId(),
            'name' => ($type == 'page') ? $item->getTitle() : $item->getName(),
            'type' => $type,
            'sort' => 0,
            'parent' => null,
        ];
    }

    private function createArray($contentsNav)
    {
        if (!empty($contentsNav)) {
            $arrayContent = [];

            foreach ($contentsNav as $item) {
                $arrayContent[$item['name']] = $item['idElement'];
            }

            return $arrayContent;
        } else {
            return null;
        }
    }
}