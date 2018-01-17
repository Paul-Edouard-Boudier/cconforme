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
        // supress html and php tag and then 'trim' supress whitespace from beggining and ending of string
        $term = trim(strip_tags($searchParam));
        // check if first element of string is a number, if it is, they sort of supress the entire number
        if (is_numeric(mb_substr($term, 0, 1))) {
            $term = explode(" ",$term);
            // In case of change: i need to retrieve the term[0] becasue it is vital to retrieve the entity at the end
            // it holds the numero_voie
            $numero = $term[0];
            unset($term[0]);
            $term = implode(" ",$term);
        }
        // I think that they thought that sizeof return the length of a string, 
        // but given what's written here i think they are just dumb...
        // sizeof(string) = 1 everytime, so here:
        // mb_substr return the char of string $term at position 1 -5, and take the first one 
        // so we verifiy this condition if a postal code is in the string
        if (is_numeric(mb_substr($term, sizeof($term) - 5, sizeof($term)))) {
            // here we kinda supress the postal code from the string
            // because from 0 to index 1-6
            $term = mb_substr($term, 0, sizeof($term) - 6);
        }
        $em = $this->getDoctrine()->getManager();
        // return the first one that is find where nom_voie = $term, so it works for the first one and then it's complete trash
        // return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(["listeerpNomVoie" => $term]);
        // TODO: adjust the selection of item
        if (!isset($numero)) {
            return null;
        } else {
            return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy([
                "listeerpNomVoie" => $term, 
                "listeerpNumeroVoie" => $numero
            ]);
        }
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
    // It's for autocomplete
    protected function getErpByType($type, $searchParam) {
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
