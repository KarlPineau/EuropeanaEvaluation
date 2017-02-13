<?php

namespace MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProposalController extends Controller
{
    public function indexAction($experiment_session_id)
    {
        try {
            $session = $this->get('api.session')->get($experiment_session_id);
            if($session == null) {return $this->redirectToRoute('messenger_system_restart');}

            $session->setEndDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            if($session->getType() == 'browseEvaluation') {
                //$proposalBrowse = $this->get('api.proposal_browse')->create($session);
                //$proposalBrowseItems = $this->get('api.proposal_browse_items')->create($proposalBrowse);
            } elseif($session->getType() == 'singleEvaluation') {
                $proposalSingle = $this->get('api.proposal_single')->create($session);
                return $this->redirectToRoute('messenger_proposal_single_index', array('authToken' => '8u0QYy8OTLVKZK8', 'experiment_proposal_single_id' => $proposalSingle->getId()));
            }
        } catch(\Exception $e) {
            return $this->redirectToRoute('messenger_system_restart');
        }
    }
}
