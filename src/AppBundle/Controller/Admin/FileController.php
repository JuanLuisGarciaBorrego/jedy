<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        return $this->render('/admin/file/admin_file_index.html.twig');
    }
}