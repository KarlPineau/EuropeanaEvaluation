<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProposalController extends Controller
{
    public function indexAction($experiment_sequence_id)
    {
        try {
            $sequence = $this->get('api.sequence')->get($experiment_sequence_id);
            if($sequence == null) {return $this->redirectToRoute('messenger_system_restart');}

            $session = $sequence->getSession();

            if($sequence->getType() == 'browseEvaluation') {
                $proposalBrowse = $this->get('api.proposal_browse')->create($sequence);
                $proposalBrowseItems = $this->get('api.proposal_browse_items')->create($proposalBrowse);
            } elseif($sequence->getType() == 'browseEvaluation') {
                $proposalSingle = $this->get('api.proposal_single')->create($sequence);
            }

            $messages = [
                "set_attributes" =>
                    [
                        "experiment_session_id" => $session->getId(),
                        "experiment_sequence_id" => $sequence->getId(),
                        "experiment_proposal_id" => $proposal->getId(),
                    ],
                "messages" => [
                    ["text" => "Creation of the proposal. Need to add text here."],
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
