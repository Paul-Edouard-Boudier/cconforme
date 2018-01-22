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
    protected function getSingleErp($id) {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(['listeerpId' => intval($id)]);
    }
    protected function parseErpEntity($erpEntity) {
        $entity = [
            'name' => $erpEntity->getListeErpNomErp(),
            'adress' => $erpEntity->getListeerpNomVoie(),
            'commune' => $erpEntity->getListeerpNomCommune(),
            'demandeur' => $erpEntity->getListeerpDemandeur(),
            'date' => $erpEntity->getListeerpDateValidAdap(),
            'delai' => $erpEntity->getListeerpDelaiAdap(),
            'lat' => $erpEntity->getListeerpLatitude(),
            'lng' => $erpEntity->getListeerpLongitude(),
        ];
        if ($erpEntity->getListeerpNumeroVoie() != '') $entity['adress'] = $erpEntity->
            getListeerpNumeroVoie() . ' ' . $erpEntity->getListeerpNomVoie() . ' ' .
            $erpEntity->getListeerpCodePostal();
        return $entity;
    }
    // It's for autocomplete
    protected function getErpByType($type, $searchParam) {
        // not sure if true (not my code) but:
        // that thing is to supress html and php tag then delete white space from beggining and end of string
        // then check if first term is a number, then delete it (i think so)
        $term = trim(strip_tags($searchParam));
        if (is_numeric(mb_substr($term, 0, 1))) {
            $term = explode(" ",$term);
            unset($term[0]);
            $term = implode(" ",$term);
        }
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('a')
        //  order by listeErpNomErp
            ->where('a.'.$type.' like :value')
            ->setParameter('value',  str_replace(' ', '_','%'.$term.'%'))
            ->orderBy('a.'.$type.'', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // It's for autocomplete
    protected function parseErpEntities($erpEntity) {
        $entities = [];
        foreach($erpEntity as $key => $entity) {
            $entities[$key] = [
                'name' => $entity->getListeErpNomErp(),
                'adress' => $entity->getListeerpNomVoie(),
                'id' => $entity->getListeerpId(),
            ];
            if ($entity->getListeerpNumeroVoie() != '') $entities[$key]['adress'] = $entity->
                getListeerpNumeroVoie() . ' ' . $entity->getListeerpNomVoie() . ' ' .
                $entity->getListeerpCodePostal();
        }
        return $entities;
    }
}
