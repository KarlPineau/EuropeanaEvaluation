<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction($authtoken)
    {
        return $this->render('AdminBundle:Home:index.html.twig', array('authtoken' => $authtoken));
    }
}
