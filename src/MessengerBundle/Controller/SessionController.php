<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    public function indexAction($user_id, $user_locale, $user_ref, $user_timezone)
    {
        try {
            $session = $this->get('api.session')->create($user_id, $user_locale, $user_ref, $user_timezone);

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_session_id" => $session->getId(),
                    ],
                "messages" => [
                    ["text" => "Starting session ".$session->getId()],
                ]
            ];

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }

    public function loadAction($experiment_session_id)
    {
        try {
            $session = $this->get('api.session')->get($experiment_session_id);
            if($session == null) {return $this->redirectToRoute('messenger_system_restart');}

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_session_id" => $session->getId(),
                    ],
                "messages" => [
                    ["text" => "Continue session ".$session->getId()],
                ]
            ];

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }
}
