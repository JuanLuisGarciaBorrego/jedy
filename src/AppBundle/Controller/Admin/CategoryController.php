<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
            ['locale' => $this->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/category/admin_category_index.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    /**
     * @Route("y/new/", name="admin_category_new")
     */
    public function newAction(Request $request)
    {
        $category = new Category($this->get('locales')->getLocaleActive());

        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_category_home');
        }

        return $this->render(
            'admin/category/admin_category_new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("y/{id}/edit/", name="admin_category_edit")
     */
    public function editAction(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_category_home');
        }

        return $this->render(
            'admin/category/admin_category_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("y/{id}/translations/", name="admin_category_translations")
     */
    public function translationsAction(Category $category)
    {
        return $this->render(
            'admin/category/admin_category_translations.html.twig',
            [
                'category' => $category,
                'translations' => $this->get('locales')->getLocales(),
                'active' => $this->get('locales')->getLocaleActive(),
            ]
        );
    }

    /**
     * @Route("y/{id}/translations/add/{localeCategory}/{localeTranslation}", name="admin_category_translation_add")
     */
    public function addTranslationAction(Request $request, Category $category, $localeTranslation)
    {
        $newCategory = new Category($localeTranslation);

        $form = $this->createForm(CategoryForm::class, $newCategory, ['parent' => $category]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($newCategory);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_category_translations', ['id' => $category->getId()]);
        }

        return $this->render(
            'admin/category/admin_category_new.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category
            ]
        );
    }

    /**
     * @Route("y/{idParent}/translations/{id}/edit/{localeCategory}/{localeTranslation}", name="admin_category_translation_edit")
     */
    public function editTranslationAction(Request $request, $idParent, Category $category, $localeTranslation)
    {
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_category_home');
        }

        return $this->render(
            'admin/category/admin_category_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}