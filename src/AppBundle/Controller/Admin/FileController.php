<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\FileForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/file")
 */
class FileController extends Controller
{
    /**
     * @Route("s/", name="admin_file_home", defaults={"page" = 1})
     * @Route("s/page-{page}", name="admin_file_home_page", requirements={"page" : "\d+"})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $page = $page < 1 ? 1 : $page;

        $itemsPerPage = 10;

        $finder = new Finder();
        $finder->files()->in($this->getParameter('uploads_directory'));

        $iterator = iterator_to_array($finder->getIterator());
        $total = $finder->count();
        $totalPages = ceil($total / $itemsPerPage);

        if ($totalPages != 0 && ($page > $totalPages)) {
            throw new NotFoundHttpException('There are only '.$totalPages.' pages to show');
        }

        $start = ($page - 1) * $itemsPerPage;

        return $this->render('/admin/file/admin_file_index.html.twig', [
            'files' => array_slice($iterator, $start, $itemsPerPage),
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/upload", name="admin_file_upload")
     * @Method({"GET", "POST"})
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createForm(FileForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['file']->getData();

            $newName = $this->get('cocur_slugify')->slugify($form['name']->getData()).'.'.$file->getClientOriginalExtension();
            $file->move($this->getParameter('uploads_directory'), $newName);

            $this->addFlash('success', 'file.flash.created');

            return $this->redirectToRoute('admin_file_home');
        }

        return $this->render('admin/file/admin_file_upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{filename}/delete/", name="admin_file_delete")
     * @Method("GET")
     */
    public function deleteAction($filename)
    {
        $file = $this->getParameter('uploads_directory').'/'.$filename;

        if (file_exists($file)) {
            unlink($file);
            $this->addFlash('success', 'file.flash.deleted');
        }

        return $this->redirectToRoute('admin_file_home');
    }
}
