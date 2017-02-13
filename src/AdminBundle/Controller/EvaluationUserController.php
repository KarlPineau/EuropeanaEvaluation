<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EvaluationUserController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersQuery = $em->getRepository('APIBundle:EvaluationUser')->findAll();

        $users = $this->get('knp_paginator')->paginate(
            $usersQuery,
            $request->query->get('page', 1),
            300
        );

        return $this->render('AdminBundle:EvaluationUser:index.html.twig', array(
            'users' => $users
        ));
    }

    public function viewAction($user_id)
    {
        $user = $this->get('api.user')->get($user_id);
        if($user === null) { throw $this->createNotFoundException('User '.$user_id.' undefined');}

        return $this->render('AdminBundle:EvaluationUser:view.html.twig', array(
            'user' => $user
        ));
    }
}
