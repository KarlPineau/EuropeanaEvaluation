<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    public function indexAction($experiment_user_id)
    {
        try {
            $user = $this->get('api.user')->get($experiment_user_id);
            if($user == null) {return $this->redirectToRoute('messenger_system_restart');}

            $lastSession = $this->get('api.user')->getLastSession($user);
            if($lastSession == null OR ($lastSession != null AND date_diff(new \DateTime(), $lastSession->getCreateDate())->format('%h') > 0)) {
                $session = $this->get('api.session')->create($user);
            } else {
                $session = $lastSession;
            }

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $user->getId(),
                        "experiment_session_id" => $session->getId(),
                        "experiment_session_type" => $session->getType(),
                    ],
                "messages" => [
                    ["text" => "This is the message of the session set. Need to explicit here the rules of parameters"],
                ]
            ];

            $session->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }
}
