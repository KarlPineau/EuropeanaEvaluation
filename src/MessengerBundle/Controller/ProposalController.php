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


            if($sequence->getType() == 'browseEvaluation') {
                $proposalBrowse = $this->get('api.proposal_browse')->create($sequence);
                $proposalBrowseItems = $this->get('api.proposal_browse_items')->create($proposalBrowse);
            } elseif($sequence->getType() == 'singleEvaluation') {
                $proposalSingle = $this->get('api.proposal_single')->create($sequence);
                return $this->redirectToRoute('messenger_proposal_single_index', array('authToken' => '8u0QYy8OTLVKZK8', 'experiment_proposal_single_id' => $proposalSingle->getId()));
            }
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }
}
