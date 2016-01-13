<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AppController extends Controller
{
    /**
     * @Route("/", name="app_index")
     */
    public function indexAction()
    {
        return $this->render(
            'app/app_index.html.twig',
            [
                'posts' => $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(
                    ['type' => 'post', 'locale' => $this->get('request_stack')->getMasterRequest()->get('_locale')]
                ),
            ]
        );
    }
}
