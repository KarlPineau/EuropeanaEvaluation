<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SystemController extends Controller
{
    public function restartAction()
    {
        $messages = [
            "messages" => [
                [
                    "text" => "Oups... An error occurred. Please restart the chatbot by clicking the following button",
                    "quick_replies" =>
                    [
                        [
                            "title" => "Restart chatbot",
                            "block_names" => ["0-Introduction"]
                        ]
                    ]
                ],
            ]
        ];

        $response = new Response(json_encode($messages));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
