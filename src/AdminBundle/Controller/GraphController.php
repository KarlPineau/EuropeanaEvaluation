<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\EntitiesFetch;
use AdminBundle\Form\EntitiesFetchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GraphController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('EntityBundle:EntityFetch')->findByProcessed(true);

        $objects = array();
        foreach($entities as $entityFetch) {
            $objects[] = $this->get('entity.graph')->buildObject($entityFetch->getUri());
        }

        return $this->render('AdminBundle:Graph:index.html.twig', array(
            'objects' => $objects
        ));
    }
}
