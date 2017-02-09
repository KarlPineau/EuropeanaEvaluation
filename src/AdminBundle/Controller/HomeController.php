<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sessionsQuery = $em->getRepository('APIBundle:EvaluationSession')->findAll();

        $sessions = $this->get('knp_paginator')->paginate(
            $sessionsQuery,
            $request->query->get('page', 1),
            300
        );

        return $this->render('AdminBundle:Home:index.html.twig', array(
            'sessions' => $sessions
        ));
    }
}
