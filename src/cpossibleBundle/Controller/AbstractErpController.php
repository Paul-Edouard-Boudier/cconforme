<?php
/**
 * Created by PhpStorm.
 * User: hugoLambrinidis
 * Date: 19/12/2017
 * Time: 15:17
 */
namespace cpossibleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
abstract class AbstractErpController extends Controller
{
  // I don't know what's thats for, no time to go deep in it
    protected function getSingleErp($searchParam) {
        $term = trim(strip_tags($searchParam));
        if (is_numeric(mb_substr($term, 0, 1))) {
            $term = explode(" ",$term);
            unset($term[0]);
            $term = implode(" ",$term);
        }
        if (is_numeric(mb_substr($term, sizeof($term) - 5, sizeof($term)))) {
            $term = mb_substr($term, 0, sizeof($term) - 6);
        }
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy([
            "listeerpNomVoie" => $term
        ]);
    }
    // I don't know what's thats for either
    protected function getErpByType($type, $searchParam) {
        $term = trim(strip_tags($searchParam));
        if (is_numeric(mb_substr($term, 0, 1))) {
            $term = explode(" ",$term);
            unset($term[0]);
            $term = implode(" ",$term);
        }
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('a')
            ->where('a.'.$type.' like :value')
            ->setParameter('value',  str_replace(' ', '_','%'.$term.'%'))
            ->getQuery()
            ->getResult();
    }
    protected function parseErpEntity($erpEntity) {
        $entity = [
            'name' => $erpEntity->getListeErpNomErp(),
            'adress' => $erpEntity->getListeerpNomVoie(),
            'type' => $erpEntity->getListeerpTypedossier(),
            'demandeur' => $erpEntity->getListeerpDemandeur(),
            'date' => $erpEntity->getListeerpDateValidAdap(),
            'delai' => $erpEntity->getListeerpDelaiAdap(),
        ];
        if ($erpEntity->getListeerpNumeroVoie() != '') $entity['adress'] = $erpEntity->
            getListeerpNumeroVoie() . ' ' . $erpEntity->getListeerpNomVoie() . ' ' .
            $erpEntity->getListeerpCodePostal();
        return $entity;
    }
    protected function parseErpEntities($erpEntity) {
        $entities = [];
        foreach($erpEntity as $key => $entity) {
            $entities[$key] = [
                'name' => $entity->getListeErpNomErp(),
                'adress' => $entity->getListeerpNomVoie(),
                'type' => $entity->getListeerpTypedossier(),
                'demandeur' => $entity->getListeerpDemandeur(),
                'date' => $entity->getListeerpDateValidAdap(),
            ];
            if ($entity->getListeerpNumeroVoie() != '') $entities[$key]['adress'] = $entity->
                getListeerpNumeroVoie() . ' ' . $entity->getListeerpNomVoie() . ' ' .
                $entity->getListeerpCodePostal();
        }
        return $entities;
    }
}
