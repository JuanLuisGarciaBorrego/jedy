<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Content;
use AppBundle\Form\ContentForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/content")
 */
class ContentController extends Controller
{
    /**
     * @Route("s/{page}/{type}", name="admin_content_home", defaults={"type" = null, "page" = 1}, requirements={"type" = "page|post"} )
     * @Method("GET")
     */
    public function indexAction($type, $page)
    {
        $pagination = $this->get('pagination')->create(
            $type,
            $page,
            $this->get('locales')->getLocaleActive()
        );

        return $this->render(
            'admin/content/admin_content_index.html.twig',
            [
                'contents' => $pagination['contents'],
                'type' => $pagination['type'],
                'total' => $pagination['total'],
                'totalPages' => $pagination['totalPages'],
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/{type}/new/", name="admin_content_new", requirements={"type" = "page|post"} )
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $type)
    {
        $content = new Content($this->get('locales')->getLocaleActive());

        $form = $this->createForm(ContentForm::class, $content, ['type' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'content.flash.created');

            return $this->redirectToRoute('admin_content_home');
        }

        return $this->render(
            'admin/content/admin_content_new.html.twig',
            [
                'form' => $form->createView(),
                'type' => $type,
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", name="admin_content_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Content $content, Request $request)
    {
        $form = $this->createForm(ContentForm::class, $content, ['type' => $content->getType()]);
        $form->handleRequest($request);

        $form_delete = $this->formDelete($content);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'content.flash.edited');

            return $this->redirectToRoute('admin_content_home');
        }

        return $this->render(
            'admin/content/admin_content_edit.html.twig',
            [
                'form' => $form->createView(),
                'type' => $content->getType(),
                'form_delete' => $form_delete->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/translations/", name="admin_content_translations")
     * @Method("GET")
     */
    public function translationsAction(Content $content)
    {
        return $this->render(
            'admin/content/admin_content_translations.html.twig',
            [
                'content' => $content,
                'translations' => $this->get('locales')->getLocales(),
                'active' => $this->get('locales')->getLocaleActive(),
            ]
        );
    }

    /**
     * @Route("/{id}/translations/add/{localeContent}/{localeTranslation}", name="admin_content_translation_add")
     * @Method({"GET", "POST"})
     */
    public function addTranslationAction(Request $request, Content $content, $localeTranslation)
    {
        $newContent = new Content($localeTranslation);

        $form = $this->createForm(
            ContentForm::class,
            $newContent,
            ['type' => $content->getType(), 'parent' => $content]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newContent);
            $em->flush();

            $this->addFlash('success', 'content.flash.translation.created');

            return $this->redirectToRoute('admin_content_translations', ['id' => $content->getId()]);
        }

        return $this->render(
            'admin/content/admin_content_new.html.twig',
            [
                'form' => $form->createView(),
                'content' => $content,
                'type' => $content->getType(),
            ]
        );
    }

    /**
     * @Route("/{idParent}/translations/{id}/edit/{localeContent}/{localeTranslation}", name="admin_content_translation_edit")
     * @Method({"GET", "POST"})
     */
    public function editTranslationAction(Request $request, $idParent, Content $content, $localeTranslation)
    {
        $form = $this->createForm(ContentForm::class, $content, ['type' => $content->getType(), 'parent' => $content]);
        $form->handleRequest($request);

        $form_delete = $this->formDelete($content);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'content.flash.translation.edited');

            return $this->redirectToRoute('admin_content_translations', ['id' => $idParent]);
        }

        return $this->render(
            'admin/content/admin_content_edit.html.twig',
            [
                'form' => $form->createView(),
                'type' => $content->getType(),
                'form_delete' => $form_delete->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete/", name="admin_content_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Content $content, Request $request)
    {
        $form = $this->formDelete($content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($content);
            $em->flush();

            $this->addFlash('success', 'content.flash.deleted');
        }

        return $this->redirectToRoute('admin_content_home');
    }

    /**
     * @param Content $content
     *
     * @return \Symfony\Component\Form\Form
     */
    private function formDelete(Content $content)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_content_delete', ['id' => $content->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
