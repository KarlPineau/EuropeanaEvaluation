<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\EntitiesFetch;
use AdminBundle\Form\EntitiesFetchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntityController extends Controller
{
    public function insertAction($authtoken, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entitiesFetch = new EntitiesFetch();
        $form = $this->createForm(EntitiesFetchType::class, $entitiesFetch);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $count = 0;
            foreach($entitiesFetch->getEntitiesFetch() as $entityFetch) {
                $entityFetch->setProcessed(false);
                $em->persist($entityFetch);
                $count++;
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', $count.' entities have been inserting properly.' );
            return $this->redirect($this->generateUrl('admin_home_index', array('authtoken' => $authtoken,)));
        }

        return $this->render('AdminBundle:Entity:insert.html.twig', array(
            'authtoken' => $authtoken,
            'form' => $form->createView(),
        ));
    }

    public function viewWaitingFetchAction($authtoken, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entitiesQuery = $em->getRepository('EntityBundle:EntityFetch')->findByProcessed(false);

        $entities = $this->get('knp_paginator')->paginate(
            $entitiesQuery,
            $request->query->get('page', 1),
            300
        );

        return $this->render('AdminBundle:Entity:viewWaitingFetch.html.twig', array(
            'entities' => $entities,
            'authtoken' => $authtoken,
        ));
    }

    public function processFetchAction($authtoken)
    {
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('EntityBundle:EntityFetch')->findByProcessed(false);

        $countTrue = 0;
        $countFalse = 0;
        foreach($entities as $entityFetch) {
            $record = $this->get('entity.process')->process($entityFetch->getUri());
            if($record != null) {
                $countTrue++;
                $entityFetch->setProcessed(true);

                $this->get('entity.similar_items')->computeSimilarity($record, 4);
            } else {
                $countFalse++;
            }
        }
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', $countTrue.' entities have been processing properly. '.$countFalse.' entities haven\'t been processing.');
        return $this->redirect($this->generateUrl('admin_home_index', array('authtoken' => $authtoken,)));
    }

    public function resetFetchAction($authtoken)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('EntityBundle:EntityFetch')->findByProcessed(true);

        foreach($entities as $entity) {
            $entity->setProcessed(false);
        }
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'The fetch list has been resetting properly.' );
        return $this->redirect($this->generateUrl('admin_home_index', array('authtoken' => $authtoken,)));
    }
}
