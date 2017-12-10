<?php

namespace cpossibleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ERPController extends Controller
{
    public function autocomplete_nameAction(Request $request)
    {
        $names = array();
        $term = trim(strip_tags($request->get('term')));

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('n')
            ->where('n.listeErpNomErp LIKE :name')
            ->setParameter('name', '%'.$term.'%')
            ->getQuery()
            ->getResult();

        foreach ($entities as $entity)
        {
            $names[] = $entity->getlisteErpNomErp();
        }

        $response = new JsonResponse();
        $response->setData($names);

        return $response;
    }

    public function autocomplete_adressAction(Request $request)
    {
        $adresses = array();
        $term = trim(strip_tags($request->get('term')));

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('a')
            ->where('a.listeerpNomVoie LIKE :adress')
            ->setParameter('adress', '%'.$term.'%')
            ->getQuery()
            ->getResult();

        foreach ($entities as $entity)
        {
            $adresses[] = $entity->getlisteerpNomVoie();
        }

        $response = new JsonResponse();
        $response->setData($adresses);

        return $response;
    }
}