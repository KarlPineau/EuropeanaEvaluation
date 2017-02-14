<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    public function indexAction($experiment_user_id)
    {
        //try {
            $user = $this->get('api.user')->get($experiment_user_id);
            //if($user == null) {return $this->redirectToRoute('messenger_system_restart');}

            $lastSession = $this->get('api.user')->getLastSession($user);
            $session = ($lastSession == null OR $lastSession->getEndSession() == true OR date_diff(new \DateTime(), $lastSession->getCreateDate())->format('%h') > 0 OR (count($this->get('api.session')->getProposals($lastSession)) > 5) AND $lastSession->getType() == 'singleEvaluation') ? $this->get('api.session')->create($user) : $lastSession;

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $user->getId(),
                        "experiment_session_id" => $session->getId(),
                        "experiment_session_type" => $session->getType(),
                    ]
            ];

            if($session != $lastSession) {
                $messages["messages"] = [
                    ["text" => "Welcome on this new session."],
                ];
            }

            $session->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        /*} catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }*/
    }

    public function closeAction($experiment_session_id)
    {
        //try {
            $previousSession = $this->get('api.session')->get($experiment_session_id);
            if($previousSession == null) {return $this->redirectToRoute('messenger_system_restart');}

            $previousSession->setEndDate(new \DateTime());
            $previousSession->setEndSession(true);
            $this->getDoctrine()->getManager()->flush();

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $previousSession->getUser()->getId(),
                    ],
                "messages" => [
                    [
                        "text" => "Thanks for your answer :)",
                        "quick_replies" => [
                            [
                                "type" => "show_block",
                                "block_names" => ["2-Session-Set"],
                                "title" => "Continue"
                            ]
                        ]
                    ],
                ]
            ];

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        /*} catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }*/
    }
}
