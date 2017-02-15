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
        $entities = $em->getRepository('EntityBundle:EntityProperty')->getAll();

        $objects = array();
        foreach($entities as $europeana_id) {
            $objects[] = $this->get('entity.graph')->buildObject($europeana_id);
        }

        return $this->render('AdminBundle:Graph:index.html.twig', array(
            'objects' => $objects
        ));
    }

    public function removeAction()
    {
        $em = $this->getDoctrine()->getManager();
        foreach($em->getRepository('EntityBundle:EntityProperty')->findAll() as $entity) {
            $em->remove($entity);
        }
        foreach($em->getRepository('EntityBundle:EntityRelation')->findAll() as $relation) {
            $em->remove($relation);
        }
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'The graph has been removing.' );
        return $this->redirect($this->generateUrl('admin_home_index'));
    }
}
