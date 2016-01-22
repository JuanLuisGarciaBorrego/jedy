<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\FileForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/file")
 */
class FileController extends Controller
{
    /**
     * @Route("s/", name="admin_file_home")
     */
    public function indexAction()
    {
        $finder = new Finder();

        $finder->files()->in($this->getParameter('uploads_directory'));

        return $this->render('/admin/file/admin_file_index.html.twig', [
            'files' => $finder
        ]);
    }

    /**
     * @Route("/upload", name="admin_file_upload")
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createForm(FileForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['file']->getData();
            
            $newName = $this->get('cocur_slugify')->slugify($form['name']->getData()).".".$file->getClientOriginalExtension();
            $file->move($this->getParameter('uploads_directory'), $newName);

            $this->addFlash('success', 'created_successfully');
            return $this->redirectToRoute('admin_file_home');
        }

        return $this->render('admin/file/admin_file_upload.html.twig', [
            'form' => $form->createView()
        ]);
    }
}