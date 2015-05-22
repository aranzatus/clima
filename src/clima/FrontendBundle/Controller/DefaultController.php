<?php

namespace clima\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('climaFrontendBundle:Default:index.html.twig');
    }
}
