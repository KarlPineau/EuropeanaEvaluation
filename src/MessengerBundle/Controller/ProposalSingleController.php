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

            $referenceItem = (object) $this->get('entity.graph')->buildObject($proposalSingle->getReferenceItem());
            $suggestedItem = (object) $this->get('entity.graph')->buildObject($proposalSingle->getSuggestedItem());

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $proposalSingle->getSession()->getUser()->getId(),
                        "experiment_session_id" => $proposalSingle->getSession()->getId(),
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
                                        "title" => $this->get('messenger.stringify')->stringify($referenceItem->dcTitle, ', ', false),
                                        "image_url" => $this->get('entity.graph')->getThumbnail($referenceItem),
                                        "subtitle" => $this->get('messenger.stringify')->stringify($referenceItem->dcDescription, ', ', false),
                                        "buttons" => [
                                            [
                                                "type" => "web_url",
                                                "url" => "http://www.europeana.eu/portal/record".$referenceItem->europeana_id.".html",
                                                "title" => "View Item"
                                            ]
                                        ]
                                    ],
                                    [
                                        "title" => $this->get('messenger.stringify')->stringify($suggestedItem->dcTitle, ', ', false),
                                        "image_url" => $this->get('entity.graph')->getThumbnail($suggestedItem),
                                        "subtitle" => $this->get('messenger.stringify')->stringify($suggestedItem->dcDescription, ', ', false),
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
                        ]
                    ],
                    [
                        "text" => "Please rate the value of this recommendation in your opinion:",
                        "quick_replies" => [
                            [
                                "set_attributes" =>
                                [
                                    "evaluation_validation_type" => "single",
                                    "evaluation_proposal_id" => $proposalSingle->getId(),
                                    "evaluation_proposal_value" => "1",
                                ],
                                "title" => "1",
                                "block_names" => ["3-Proposal-Validation"]
                            ],
                            [
                                "set_attributes" =>
                                    [
                                        "evaluation_validation_type" => "single",
                                        "evaluation_proposal_id" => $proposalSingle->getId(),
                                        "evaluation_proposal_value" => "2",
                                    ],
                                "title" => "2",
                                "block_names" => ["3-Proposal-Validation"]
                            ],
                            [
                                "set_attributes" =>
                                    [
                                        "evaluation_validation_type" => "single",
                                        "evaluation_proposal_id" => $proposalSingle->getId(),
                                        "evaluation_proposal_value" => "3",
                                    ],
                                "title" => "3",
                                "block_names" => ["3-Proposal-Validation"]
                            ],
                            [
                                "set_attributes" =>
                                    [
                                        "evaluation_validation_type" => "single",
                                        "evaluation_proposal_id" => $proposalSingle->getId(),
                                        "evaluation_proposal_value" => "4",
                                    ],
                                "title" => "4",
                                "block_names" => ["3-Proposal-Validation"]
                            ],
                            [
                                "set_attributes" =>
                                    [
                                        "evaluation_validation_type" => "single",
                                        "evaluation_proposal_id" => $proposalSingle->getId(),
                                        "evaluation_proposal_value" => "5",
                                    ],
                                "title" => "5",
                                "block_names" => ["3-Proposal-Validation"]
                            ]
                        ]
                    ]
                ]
            ];

            if($proposalSingle->getSession()->getContextualized() == true) {
                $context = 'The following items shared these properties: '.implode(', ', $this->get('api.context')->getForProposalSingle($proposalSingle));
                array_unshift($messages['messages'], ["text" => $context]);
            }


            $proposalSingle->getSession()->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

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
            $proposalSingle->getSession()->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $proposalSingle->getSession()->getUser()->getId(),
                        "experiment_load_new_proposal" => "true",
                    ],
                "messages" => [
                    [
                        "text" => "Thanks for your answer :) Let's continue.",
                        "quick_replies" => [
                            [
                                "type" => "show_block",
                                "block_names" => ["1-User"],
                                "title" => "Continue"
                            ]
                        ]
                    ],
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
