<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\ContentsNav;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use AppBundle\Entity\Nav;
use AppBundle\Form\NavContents\NavCategoryForm;
use AppBundle\Form\NavContents\NavPageForm;
use AppBundle\Form\NavForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        //$request->getSession()->remove('contents');
        $contents = $request->getSession()->has('contents') ? $request->getSession()->get('contents') : new ArrayCollection();

        $formCategory = $this->createForm(NavCategoryForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);
        $formPage = $this->createForm(NavPageForm::class, null, ['em' => $this->getDoctrine(), 'locale_active' => $this->get('locales')->getLocaleActive()]);

        $formCategory->handleRequest($request);
        $formPage->handleRequest($request);

        if (($formCategory->isSubmitted() && $formCategory->isValid()) || ($formPage->isSubmitted() && $formPage->isValid())) {

            $category = $formCategory->getData();
            $page = $formPage->getData();

            if ($category) {
                $item = $this->createArray($category['category'], 'category');
            }

            if ($page) {
                $item = $this->createArray($page['page'], 'page');
            }

            if (!$contents->contains($item)) {
                $contents->add($item);
                $request->getSession()->set('contents', $contents);
            } else {
                $this->addFlash('error', 'exits');
            }
        }

        $cnb = $this->get('form.factory')->createNamedBuilder('formSession');
        foreach ($contents as $key => $element) {
            $cnb->add('parent_id' . $key, ChoiceType::class, [
                    'choices' => $this->selectParent($contents),
                    'placeholder' => "Subcategory",
                    'group_by' => function ($val, $key, $index) {
                        //
                    }
                ]
            );
            $cnb->add('nav_id' . $key, HiddenType::class, [
                'data' => $nav->getId()
            ]);
            $cnb->add('idElement' . $key, HiddenType::class, [
                'data' => $element['idElement']
            ]);
            $cnb->add('name' . $key, HiddenType::class, [
                'data' => $element['name']
            ]);
            $cnb->add('type', HiddenType::class, [
                'data' => $element['type']
            ]);
            $cnb->add('sort' . $key, IntegerType::class, [
                'data' => $element['sort']
            ]);
        }
        $formSession = $cnb->getForm();

        return $this->render(
            'admin/nav/admin_nav_add_content.html.twig', [
                'nav' => $nav,
                'form_category' => $formCategory->createView(),
                'form_page' => $formPage->createView(),
                'contents' => $request->getSession()->get('contents'),
                'formSession' => $formSession->createView()
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

    private function createArray($item, $type)
    {
        return [
            'parent_id' => null,
            'nav_id' => $item->getId(),
            'idElement' => $item->getId(),
            'name' => ($type == 'category') ? $item->getName() : $item->getTitle(),
            'type' => $type,
            'sort' => 0
        ];
    }

    private function selectParent($contents)
    {
        $resutl = array();
        foreach ($contents as $item) {
            $resutl[] = [
                $item['name'] . " [" . $item['type'] . "]" => $item['idElement']
            ];
        }
        return $resutl;
    }
}
