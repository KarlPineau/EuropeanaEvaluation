<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProposalBrowseController extends Controller
{
    public function indexAction($experiment_proposal_browse_id)
    {
        //try {
            $proposalBrowse = $this->get('api.proposal_browse')->get($experiment_proposal_browse_id);
            //if($proposalBrowse == null) {return $this->redirectToRoute('messenger_system_restart');}

            $referenceItem = (object) $this->get('entity.graph')->buildObject($proposalBrowse->getReferenceItem());
            $suggestedItems = array();
            foreach($this->get('api.proposal_browse_item')->getByProposalBrowse($proposalBrowse) as $proposalBrowseItem) {
                $suggestedItems[] = ['record' => (object) $this->get('entity.graph')->buildObject($proposalBrowseItem->getItem()), 'proposalBrowseItem' => $proposalBrowseItem];
            }

            if(count($suggestedItems) == 0) {return $this->redirectToRoute('messenger_session_close', array('authToken' => '8u0QYy8OTLVKZK8', 'experiment_session_id' => $proposalBrowse->getSession()->getId()));}

            $elements = [];
            foreach($suggestedItems as $suggestedItemContainer) {
                $elements[] = [
                    "title" => $this->get('messenger.stringify')->stringify($suggestedItemContainer['record']->dcTitle, ', ', false),
                    "image_url" => $this->get('entity.graph')->getThumbnail($suggestedItemContainer['record']),
                    "subtitle" => $this->get('messenger.stringify')->stringify($suggestedItemContainer['record']->dcDescription, ', ', false),
                    "buttons" => [
                        [
                            "type" => "web_url",
                            "url" => "http://www.europeana.eu/portal/record".$suggestedItemContainer['record']->europeana_id.".html",
                            "title" => "View Item"
                        ],
                        [
                            "set_attributes" =>
                                [
                                    "evaluation_validation_type" => "browse",
                                    "evaluation_proposal_id" => $proposalBrowse->getId(),
                                    "evaluation_proposal_value" => $suggestedItemContainer['proposalBrowseItem']->getId(),
                                ],
                            "title" => "Select this item",
                            "type" => "show_block",
                            "block_name" => "3-Proposal-Validation"
                        ]
                    ]
                ];
            }
            shuffle($elements);

            $elements[] = [
                "title" => "No interesting choices",
                "image_url" => "https://upload.wikimedia.org/wikipedia/commons/0/06/Face-sad.svg",
                "subtitle" => "Click here if you can't find any interesting item.",
                "buttons" => [
                    [
                        "set_attributes" =>
                            [
                                "evaluation_validation_type" => "browse",
                                "evaluation_proposal_id" => $proposalBrowse->getId(),
                                "evaluation_proposal_value" => 0,
                            ],
                        "title" => "Select this item",
                        "type" => "show_block",
                        "block_name" => "3-Proposal-Validation"
                    ]
                ]
            ];


            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $proposalBrowse->getSession()->getUser()->getId(),
                        "experiment_session_id" => $proposalBrowse->getSession()->getId(),
                        "experiment_proposal_id" => $proposalBrowse->getId(),
                        "experiment_proposal_type" => "browseEvaluation",
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
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "text" => "Please select the most similar items in your opinion",
                    ],
                    [
                        "attachment" => [
                            "type" => "template",
                            "payload" => [
                                "template_type" => "generic",
                                "elements" => $elements
                            ]
                        ]
                    ]
                ]
            ];


            $proposalBrowse->getSession()->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $response = new Response(json_encode($messages));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        /*} catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }*/
    }

    public function validationAction($experiment_proposal_browse_id, $experiment_proposal_browse_i_id)
    {
        //try {
            $proposalBrowse = $this->get('api.proposal_browse')->get($experiment_proposal_browse_id);
            if($proposalBrowse == null) {return $this->redirectToRoute('messenger_system_restart');}


            if($experiment_proposal_browse_i_id != 0) {
                $proposalBrowseItem = $this->get('api.proposal_browse_item')->get($experiment_proposal_browse_i_id);
                if ($proposalBrowseItem == null) {return $this->redirectToRoute('messenger_system_restart');}
                $proposalBrowse->setChoicedItem($proposalBrowseItem);
            } else {
                $proposalBrowse->setChoiceNull(true);
            }

            $proposalBrowse->getSession()->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_user_id" => $proposalBrowse->getSession()->getUser()->getId(),
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
        /*} catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }*/
    }
}
