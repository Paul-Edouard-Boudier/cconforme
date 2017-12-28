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

        $date = $erpEntity->getListeerpDateValidAdap();

        $delai = $erpEntity->getListeerpDelaiAdap();

        $date_explode = explode("-", $date);

        $date_explode[2] = $date_explode[2] + $delai;

        $date = implode("-", $date_explode);

        switch ($date_explode[1]) {
            case "01":
                $date = "Janvier" . " " . $date_explode[2];
                break;
            case "02":
                $date = "Février" . " " . $date_explode[2];
                break;
            case "03":
                $date = "Mars" . " " . $date_explode[2];
                break;
            case "04":
                $date = "Avril" . " " . $date_explode[2];
                break;
            case "05":
                $date = "Mai" . " " . $date_explode[2];
                break;
            case "06":
                $date = "Juin" . " " . $date_explode[2];
                break;
            case "07":
                $date = "Juillet" . " " . $date_explode[2];
                break;
            case "08":
                $date = "Août" . " " . $date_explode[2];
                break;
            case "09":
                $date = "Septembre" . " " . $date_explode[2];
                break;
            case "10":
                $date = "Octobre" . " " . $date_explode[2];
                break;
            case "11":
                $date = "Novembre" . " " . $date_explode[2];
                break;
            case "12":
                $date = "Décembre" . " " . $date_explode[2];
                break;
        }

        $entity = [
            'name' => $erpEntity->getListeErpNomErp(),
            'adress' => $erpEntity->getListeerpNomVoie(),
            'type' => $erpEntity->getListeerpTypedossier(),
            'demandeur' => $erpEntity->getListeerpDemandeur(),
            'date' => $date,
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