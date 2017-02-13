<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function indexAction($user_id, $user_locale, $user_ref, $user_timezone)
    {
        try {
            if($this->get('api.user')->getByFbId($user_id) == null) {
                $user = $this->get('api.user')->create($user_id, $user_locale, $user_ref, $user_timezone);

                $messages = [
                    "set_attributes" =>
                        [
                            "experiment_user_id" => $user->getId(),
                        ],
                    "messages" => [
                        ["text" => "Starting user " . $user->getId()],
                    ]
                ];

                $response = new Response(json_encode($messages));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                return $this->redirectToRoute('messenger_user_load', array('experiment_user_id' => $this->get('api.user')->getByFbId($user_id)->getId(), 'authToken' => '8u0QYy8OTLVKZK8'));
            }
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }

    public function loadAction($experiment_user_id)
    {
        try {
            $user = $this->get('api.user')->get($experiment_user_id);
            if($user == null) {return $this->redirectToRoute('messenger_system_restart');}

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $user->getId(),
                    ],
                "messages" => [
                    ["text" => "Continue user ".$user->getId()],
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
