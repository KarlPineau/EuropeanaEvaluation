<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EvaluationUserController extends Controller
{
    public function removeAction($user_id)
    {
        $user = $this->get('api.user')->get($user_id);
        if($user === null) { throw $this->createNotFoundException('User '.$user_id.' undefined.');}

        foreach($this->get('api.session')->getByUser($user) as $session){
            foreach($this->get('api.proposal_single')->getBySession($session) as $proposal) {
                $this->get('api.proposal_single')->remove($proposal);
            }
            foreach($this->get('api.proposal_browse')->getBySession($session) as $proposal) {
                $this->get('api.proposal_browse')->remove($proposal);
            }
            $this->getDoctrine()->getManager()->remove($session);
        }

        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('notice', 'User '.$user_id.' has been correctly removing.' );
        return $this->redirectToRoute('admin_home_index');
    }
}
