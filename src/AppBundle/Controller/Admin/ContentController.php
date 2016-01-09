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
     * @Route("s/", name="admin_content_home")
     */
    public function indexAction()
    {
        $contents = $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(
            ['locale' => $this->container->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/content/admin_content_index.html.twig',
            [
                'contents' => $contents,
            ]
        );
    }

    /**
     * @Route("/{type}/new/", name="admin_content_new")
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

            $this->addFlash('success', 'created_successfully');

            return $this->redirectToRoute('admin_content_home');
        }

        return $this->render(
            'admin/content/admin_content_new.html.twig',
            [
                'form' => $form->createView(),
                'type' => $type
            ]
        );
    }
}