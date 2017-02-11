<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProposalSingleController extends Controller
{
    public function indexAction($experiment_proposal_single_id)
    {
        try {
            $proposalSingle = $this->get('api.proposal_single')->get($experiment_proposal_single_id);
            if($proposalSingle == null) {return $this->redirectToRoute('messenger_system_restart');}

            $referenceItem = (object) $this->get('entity.graph')->buildObjectById($proposalSingle->getReferenceItem());
            $suggestedItem = (object) $this->get('entity.graph')->buildObjectById($proposalSingle->getSuggestedItem());

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_session_id" => $proposalSingle->getSequence()->getSession()->getId(),
                        "experiment_sequence_id" => $proposalSingle->getSequence()->getId(),
                        "experiment_proposal_id" => $proposalSingle->getId(),
                        "experiment_proposal_type" => "singleEvaluation",
                    ],
                "messages" => [
                    [
                        "attachment" => [
                            "type" => "template",
                            "payload" => [
                                "template_type" => "generic",
                                "elements" => [
                                    [
                                        "title" => $this->get('messenger.stringify')->stringify($referenceItem->dcTitle),
                                        "image_url" => $this->get('messenger.stringify')->stringify($referenceItem->edmIsShownBy),
                                        "subtitle" => $this->get('messenger.stringify')->stringify($referenceItem->dcDescription),
                                        "buttons" => [
                                            [
                                                "type" => "web_url",
                                                "url" => "http://www.europeana.eu/portal/record".$referenceItem->europeana_id.".html",
                                                "title" => "View Item"
                                            ]
                                        ]
                                    ],
                                    [
                                        "title" => $this->get('messenger.stringify')->stringify($suggestedItem->dcTitle),
                                        "image_url" => $this->get('messenger.stringify')->stringify($suggestedItem->edmIsShownBy),
                                        "subtitle" => $this->get('messenger.stringify')->stringify($suggestedItem->dcDescription),
                                        "buttons" => [
                                            [
                                                "type" => "web_url",
                                                "url" => "http://www.europeana.eu/portal/record".$suggestedItem->europeana_id.".html",
                                                "title" => "View Item"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "text" => "Please rate the similarity of these two items",
                            "quick_replies" => [
                                [
                                    "title" => "1",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/1",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "2",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/2",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "3",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/3",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "4",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/4",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "5",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/5",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "6",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/6",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "7",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/7",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "8",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/8",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "0",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/9",
                                    "type" => "json_plugin_url",
                                ],
                                [
                                    "title" => "10",
                                    "url" => "http://europeana-evaluation.karl-pineau.fr/messenger/proposal/single/validation/8u0QYy8OTLVKZK8/".$proposalSingle->getId()."/10",
                                    "type" => "json_plugin_url",
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }

    public function validationAction($experiment_proposal_single_id, $value)
    {
        try {
            $proposalSingle = $this->get('api.proposal_single')->get($experiment_proposal_single_id);
            if($proposalSingle == null) {return $this->redirectToRoute('messenger_system_restart');}
            if($value != '1' AND $value != '2' AND $value != '3' AND $value != '4' AND $value != '5' AND $value != '6' AND $value != '7' AND $value != '8' AND $value != '9' AND $value != '10') {return $this->redirectToRoute('messenger_system_restart');}

            $proposalSingle->setRateValue(intval($value));

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
