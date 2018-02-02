<?php

namespace cpossibleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Datetime;
use \DateInterval;
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
            getListeerpNumeroVoie() . ' ' . $erpEntity->getListeerpNomVoie() . ', ' .
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
                getListeerpNumeroVoie() . ' ' . $entity->getListeerpNomVoie() . ', ' .
                $entity->getListeerpCodePostal();
        }
        return $entities;
    }

    protected function getNormalizedAddress($address) {
        $em = $this->getDoctrine()->getManager();
        $tempAdress = $address; // Here: "Place de l'Europe" (whithout whitespcae at the end)
        $adressExploded = explode(" ",$tempAdress);
        $intitule_voie = $adressExploded[0]; // "Place"
        $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
        $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
        ->setParameter('intitulevoieNom', $intitule_voie);
        $result = $q->getQuery();
        // Here we want to get the nom de voie as we wish to put in ddb like "PL"
        $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
        $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
        $fulladdress = "";
        $fulladdress .= $voie;
        for ($i=1; $i < count($adressExploded) ; $i++) {
          $fulladdress .= " " .strtoupper($adressExploded[$i]);
        }
        $fulladdress = str_replace('DR', 'DOCTEUR', $fulladdress);
        return $fulladdress;
    }

    protected function getAccessibility($erp) {
      $erp['accessible'] = 'est accessible';
      if ($erp['listeerpDateValidAdap'] != null && $erp['listeerpDelaiAdap'] != null) {
        $erp['accessible'] = null;
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        $date = $erp['listeerpDateValidAdap'];
        $delai = $erp['listeerpDelaiAdap'];
        $dt = DateTime::createFromFormat("Y-m-d", $date);
        if ($dt !== false && !array_sum($dt->getLastErrors())) {
        //
        // $date = new DateTime($date);
        // $erp['mois'] = ucfirst(strftime("%B", $date->getTimestamp()));
        // //
        // $date->add(new DateInterval('P'.$delai.'Y'));
        // $newdate =  $date->format('d-m-Y');
        // $time = strtotime($newdate);
        // $erp['annee'] = date("Y",$time);

        $datetime = new DateTime($date);
        $date = $datetime->createFromFormat('Y-m-d', $date);
        $erp['mois'] = ucfirst(strftime("%B", $date->getTimestamp()));
        $date->add(new DateInterval('P'.$delai.'Y'));
        $newdate =  $date->format('d-m-Y');
        $time = strtotime($newdate);
        $erp['annee'] = date("Y",$time);
        $now = new DateTime();
        $nowDate = date('d-m-Y');
        $checkNow = strtotime(date('d-m-Y'));
        $checkNow >= $time ? $erp['accessible'] = 'accessible':$erp['accessible'] = null;
        } 
        else {
          $erp['mois'] = null;
          $erp['annee'] = null;
          return $erp;
        }
      }
      return $erp;
    }
}
