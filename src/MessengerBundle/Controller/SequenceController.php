<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SequenceController extends Controller
{
    public function indexAction($experiment_session_id)
    {
        try {
            $session = $this->get('api.session')->get($experiment_session_id);
            if($session == null) {return $this->redirectToRoute('messenger_system_restart');}

            $sequence = $this->get('api.sequence')->create($session);

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_session_id" => $session->getId(),
                        "experiment_sequence_id" => $sequence->getId(),
                    ],
                "messages" => [
                    ["text" => "This is the message of the sequence set. Need to explicit here the rules of parameters"],
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
