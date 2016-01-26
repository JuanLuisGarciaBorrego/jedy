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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

        return $this->render('admin/nav/admin_nav_index.html.twig', [
            'navs' => $this->getDoctrine()->getRepository('AppBundle:Nav')->findBy(['locale' => $this->get('locales')->getLocaleActive()]),
        ]);
    }

    /**
     * @Route("/new/", name="admin_nav_new")
     * @Route("/new/{idRemove}", name="admin_nav_new_remove_element", requirements={"idRemove" : "\d+"})
     */
    public function newAction(Request $request, $idRemove = null)
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
                $this->createContentsNav($sessionContent, $nav);
            } else {
                $this->addFlash('error', 'nav.flash.exists');
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

            $this->addFlash('success', 'nav.flash.created');

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

    /**
     * @Route("/edit/{id}", name="admin_nav_edit")
     * @Route("/edit/{id}/{idRemove}", name="admin_nav_edit_remove_element", requirements={"idRemove" : "\d+"})
     */
    public function editAction(Request $request, Nav $nav = null, $idRemove = null)
    {
        $sessionContents = $request->getSession()->has('contents') ? $request->getSession()->get('contents') : new ArrayCollection();

        $form_delete = $this->formDelete($nav);

        if ($idRemove) {
            foreach ($sessionContents as $item) {
                if ($item['idElement'] == $idRemove) {
                    $sessionContents->removeElement($item);
                    $this->getDoctrine()->getRepository('AppBundle:ContentsNav')->removeNavContentByIdElement($idRemove, $nav);

                    return $this->redirectToRoute('admin_nav_edit', ['id' => $nav->getId()]);
                }
            }
        }

        foreach ($nav->getContentsNav()->toArray() as $item) {
            $contentsNavBD = [
                'idElement' => $item->getIdElement(),
                'name' => $item->getName(),
                'slug' => $item->getSlug(),
                'type' => $item->getType(),
                'sort' => $item->getSort(),
                'parent' => $item->getParent(),
            ];
            $sessionContents->add($contentsNavBD);
        }

        $request->getSession()->set('contents', $sessionContents);

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
                $this->addFlash('error', 'nav.flash.exists');
            }
        }

        $formNavContents = $this->createForm(NavForm::class, $nav, ['contentsNav' => $this->createArray($sessionContents)]);
        $formNavContents->handleRequest($request);

        if ($formNavContents->isSubmitted() && $formNavContents->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nav);
            $em->flush();

            $request->getSession()->remove('contents');

            $this->addFlash('success', 'nav.flash.edited');

            return $this->redirectToRoute('admin_nav_home');
        }

        return $this->render(
            'admin/nav/admin_nav_edit.html.twig',
            [
                'formNavContents' => $formNavContents->createView(),
                'form_category' => $formCategory->createView(),
                'form_page' => $formPage->createView(),
                'form_delete' => $form_delete->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete/", name="admin_nav_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Nav $nav, Request $request)
    {
        $form = $this->formDelete($nav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nav);
            $em->flush();

            $this->addFlash('success', 'nav.flash.deleted');
        }

        return $this->redirectToRoute('admin_nav_home');
    }

    /**
     * @param Nav $nav
     *
     * @return \Symfony\Component\Form\Form
     */
    private function formDelete(Nav $nav)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_nav_delete', ['id' => $nav->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/{id}/translations/", name="admin_nav_translations")
     * @Method("GET")
     */
    public function translationsAction(Nav $nav)
    {
        return $this->render(
            'admin/nav/admin_nav_translations.html.twig',
            [
                'nav' => $nav,
                'translations' => $this->get('locales')->getLocales(),
                'active' => $this->get('locales')->getLocaleActive(),
            ]
        );
    }

    /**
     * @Route("/{id}/translations/generate/{localeNav}/{localeTranslation}", name="admin_nav_translation_generate")
     * @Method("GET")
     */
    public function generateTranslationAction(Nav $nav, $localeTranslation)
    {
        $translationNav = $this->generateTranslation($nav, $localeTranslation);

        $em = $this->getDoctrine()->getManager();
        $em->persist($translationNav);
        $em->flush();

        $this->addFlash('success', 'nav.flash.translation.generated');

        return $this->redirectToRoute('admin_nav_translations', ['id' => $nav->getId()]);
    }

    /**
     * @Route("/{id}/translations/update/{localeNav}/{localeTranslation}", name="admin_nav_translation_update")
     * @ParamConverter("nav", class="AppBundle:Nav", options={
     *      "id" : {"id", "localeTranslation"},
     *      "repository_method" = "removeTranslationNavByIdElement"
     * })
     * @Method("GET")
     */
    public function updateTranslationAction(Nav $nav, $localeTranslation)
    {
        $newNav = clone $nav;

        $em = $this->getDoctrine()->getManager();
        $em->remove($nav);
        $em->flush();

        $newNav = $this->getDoctrine()->getRepository('AppBundle:Nav')->findOneBy(['id' => $newNav->getParentMultilangue()->getId()]);

        $translationNav = $this->generateTranslation($newNav, $localeTranslation);

        $em->persist($translationNav);
        $em->flush();

        $this->addFlash('success', 'nav.flash.translation.updated');

        return $this->redirectToRoute('admin_nav_translations', ['id' => $newNav->getId()]);
    }

    private function createContentsNav($sessionContent, Nav $nav)
    {
        $contentsNav = new ContentsNav();
        $contentsNav->setIdElement($sessionContent['idElement']);
        $contentsNav->setName($sessionContent['name']);
        $contentsNav->setSlug($sessionContent['slug']);
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
            'slug' => $item->getSlug(),
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
            return;
        }
    }

    private function generateTranslation(Nav $nav, $localeTranslation)
    {
        $translationNav = new Nav($localeTranslation);
        $translationNav->setName($nav->getName());
        $translationNav->setLocale($localeTranslation);
        $translationNav->setParentMultilangue($nav);
        $translationNav->setParentMultilangue($nav);

        foreach ($nav->getContentsNav() as $item) {
            if ($item->getType() == 'category') {
                $category = $this->getDoctrine()->getRepository('AppBundle:Category')->selectCategoryParent($item->getIdElement(), $localeTranslation);

                $contentCategory = new ContentsNav();
                $contentCategory->setIdElement($category->getId());
                $contentCategory->setName($category->getName());
                $contentCategory->setSlug($category->getSlug());
                $contentCategory->setType('category');
                $contentCategory->setSort($item->getSort());

                $parentCategory = $this->getDoctrine()->getRepository('AppBundle:Category')->selectCategoryParent($item->getParent(), $localeTranslation);

                $contentCategory->setParent(($parentCategory) ? $parentCategory->getId() : null);
                $contentCategory->setNav($translationNav);

                $translationNav->addContentsNav($contentCategory);
            }

            if ($item->getType() == 'page') {
                $page = $this->getDoctrine()->getRepository('AppBundle:Content')->selectContentParent($item->getIdElement(), $localeTranslation, 'page');

                $contentPage = new ContentsNav();
                $contentPage->setIdElement($page->getId());
                $contentPage->setName($page->getTitle());
                $contentPage->setSlug($page->getSlug());
                $contentPage->setType('page');
                $contentPage->setSort($item->getSort());

                $parentPage = $this->getDoctrine()->getRepository('AppBundle:Content')->selectContentParent($item->getParent(), $localeTranslation, 'page');

                $contentPage->setParent(($parentPage) ? $parentPage->getId() : null);
                $contentPage->setNav($translationNav);
                $translationNav->addContentsNav($contentPage);
            }
        }

        return $translationNav;
    }
}
