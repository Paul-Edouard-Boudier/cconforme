<?php

namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Datetime;
use \DateInterval;

class HomeController extends AbstractErpController
{

    public function aroundAction(Request $request) {
      if ($request->isXMLHttpRequest()) {
        $lat = floatval($request->get('lat'));
        $lng = floatval($request->get('lng'));
        // limit = number of items i want to display
        $limit = intval($request->get('limit'));
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('erp')->getQuery()->getArrayResult();
        // $conn = $this->getDoctrine()->getManager()
        //               ->getConnection();
        // $sql = "SELECT * FROM resicadminresic.dba_listeERP;";
        // $stmt = $conn->prepare($sql);
        // $stmt->execute();
        // $result = $stmt->fetchAll();
        $actualLocation = ['listeerpLatitude' => $lat, 'listeerpLongitude' => $lng, 'listeErpNomErp' => 'Point de départ du rayon de recherche'];

        // we could set this dynamically aswell
        // the distance from the point where we are actually seraching items
        $rayon = 0.002;
        $markers = []; // The array that hold every single item retrieved by query
        // Here we calculate the distance for each point to the center
        // that is the point where the user is searching
        // and then we push into [markers], every items that we want
        foreach ($result as $item) {
          $item = $this->getAccessibility($item);
          if ($item['listeerpNumeroVoie'] != '') $item['address'] = $item['listeerpNumeroVoie'] . ' ' . $item['listeerpNomVoie'] . ', ' .
            $item['listeerpCodePostal'].' '.$item['listeerpNomCommune'];
          $ilat = floatval($item['listeerpLatitude']);
          $ilong = floatval($item['listeerpLongitude']);
          $distanceAuCarre = (($ilat - $lat) ** 2) + (($ilong - $lng) ** 2);
          $distance = sqrt($distanceAuCarre);
          if ($distance <= $rayon) {
            $item['distance'] = $distance;
            array_push($markers, $item);
          }
        }
        //Here we setup an array of distance to sort [markers] via the distance, ascendingly
        $distance = [];
        foreach ($markers as $key => $row) {
          $distance[$key] = $row['distance'];
        }
        array_multisort($distance, SORT_ASC, $markers);
         // The array i want to display on the map, sliced by the limit, so we can change it dynamically
        $markersSlicedWithLimit = array_slice($markers, 0, $limit, true);
        array_push($markersSlicedWithLimit, $actualLocation);
        return new JsonResponse($markersSlicedWithLimit);
      } else {
        return "Failed";
      }
    }


    // Function that display all erp that the user want to see
    /**
     * @param $request
     * @return JSON response with all erps
     */
    public function search_listAction(Request $request) {
      if ($request->isXMLHttpRequest()) {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
        $queryBuilder
            ->andWhere('dba.listeerpNomCommune LIKE :Commune')
            ->andWhere('dba.listeErpNomErp LIKE :Nom')
            ->setParameter('Commune', '%' . $request->get('commune') . '%' )
            ->setParameter('Nom', '%' . $request->get('nom') . '%');
        if ($request->get('type') !== 'null') {
          $queryBuilder->andWhere('dba.listeerpType LIKE :Type')
            ->setParameter('Type', '%' . $request->get('type') . '%');
        }
        $result = $queryBuilder->getQuery()->getArrayResult();
        // we get it into an array so we can modify the entity and then add month and year
        // so we can display it properly on homepage for the user
        $erps = [];
        // here we want to know if an erp is accessible or not and then push the entity into antoher array to return
        foreach ($result as $erp) {
          $erp = $this->getAccessibility($erp);
        //   $erp['accessible'] = 'est accessible';
        //   if ($erp['listeerpDateValidAdap'] != null && $erp['listeerpDelaiAdap'] != null) {
        //     $erp['accessible'] = null;
        //     setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        //     $date = $erp['listeerpDateValidAdap'];
        //     $delai = $erp['listeerpDelaiAdap'];
        //     //
        //     $date = new DateTime($date);
        //     $erp['mois'] = ucfirst(strftime("%B", $date->getTimestamp()));
        //     //
        //     $date->add(new DateInterval('P'.$delai.'Y'));
        //     $newdate =  $date->format('d-m-Y');
        //     $time = strtotime($newdate);
        //     $erp['annee'] = date("Y",$time);
        //   }
          array_push($erps, $erp);
        }
        return new JsonResponse($erps);
      }
      else {
        return "Failed";
      }
    }


    public function fetchAction ()
    {
        $request = Request::createFromGlobals();
        $search = $request->request;
        // data is holding the id of the erp that the user clicked on
        if($request->isMethod('post') &&
            $search->get('data')
        ) {
            $response = $this->getSingleErp($search->get('data'));
            if ($response == null) {
                $response = ['message' => 'Aucun erp trouvé.', 'error' => true];
            } else {
                $response = $this->parseErpEntity($response);
                $response = $this->constructResponseMessage($response);
            }

            $res = new JsonResponse();
            $res->setData($response);
            return $res;
        }
        $em = $this->getDoctrine()->getManager();
        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
        $typesErp = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
            'typesErp' => $typesErp,
        ));

    }


    /**
    * @param address that comes from the google autocompletion 
    * @return array of erps that fit the address
    */
    public function erpListAutocompletedAction(Request $request) {
      if ($request->isXMLHttpRequest()) {
        $em = $this->getDoctrine()->getManager();
        $fulladdress = $this->getNormalizedAddress($request->get('address'));
        $qb = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('erp');
        $qb->andWhere('erp.listeerpNomVoie LIKE :address')
        ->andWhere('erp.listeerpNumeroVoie LIKE :number')
        ->setParameter('address', $fulladdress )
        ->setParameter('number', $request->get('number'));
        $result = $qb->getQuery()->getArrayResult();
        $erps = [];
        // here we want to know if an erp is accessible or not and then push the entity into antoher array to return
        foreach ($result as $erp) {
          $erp = $this->getAccessibility($erp);
          array_push($erps, $erp);
        }
        return new JsonResponse($erps);
      }
      else {
        return "Failed";
      }
    }


    /**
    * @param departement from homepage autocomplete
    * @return true if dpt is 'en service' false otherwise
    */
    public function checkDepartementAction(Request $request) {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $dpt = $em->getRepository('cpossibleBundle:DbaDepartement')->findOneBy(['departementNom' => $request->get('dpt')]);
            if ($dpt->getDepartementEnService() == 1) {
              $data = ['status' => true, 'message' => $dpt->getDepartementMessage()];
              return new JsonResponse($data);
            }
            $data = ['status' =>false, 'message' => $dpt->getDepartementMessage()];
            return new JsonResponse($data);
        }
        else {
            return "Failed";
        }
    }

      /**
     * @param $status 'ok' | 'ko'
     * @param null $erp ErpEntity
     * @return array
     */
    private function constructResponseMessage($erp = null) {
        $erp['accessible'] = 'est accessible';
        $lat = $erp['lat'];
        $lng = $erp['lng'];
        unset($erp['lat'], $erp['lng']);
        // unsetting keys allow me to have only 4 keys of the erp, therefore only 16 cases to check
        if ($erp['date'] != null && $erp['delai'] != null) {
            $erp['accessible'] = null;
            setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
            $date = $erp["date"];
            $delai = $erp["delai"];
            //
            $date = new DateTime($date);
            $mois = ucfirst(strftime("%B", $date->getTimestamp())); // usefull
            //
            $date->add(new DateInterval('P'.$delai.'Y'));
            $newdate =  $date->format('d-m-Y');
            $time = strtotime($newdate);
            $annee = date("Y",$time); // usefull
            $now = new DateTime();
            $nowDate = date('d-m-Y');
            $checkNow = strtotime(date('d-m-Y'));
            $checkNow >= $time ? $erp['accessible'] = 'accessible':$erp['accessible'] = null;
        }
        $nom = $erp['name'];
        unset($erp['name']);
        // if no date or no delay, then we consider that the erp is 'reachable'/accessible 
        unset($erp['date'], $erp['delai']);
        // $status is here to check wich key are filled up and looks like "1101", each number representing a key of the $erp
        // in the order given by parseEntity ('adress', 'commune', 'demandeur', 'date')
        // if date = 1 then it is 'reachable/accessible'
        $status = $this->fillVariable($erp);
        // sure enough there is another cleaner solution for this but right now i have many things to do, and not so much 
        // time to invest in finding this sort of stuff
        $response['lat'] = $lat;
        $response['lng'] = $lng;
        $response['commune'] = $erp['commune'];
        // change this fucking shit that took me like 3 hours to a single line like displayed on map:
        // L'établissement "nom" sera accessible d'ici "mois" "annee".
        // L'établissement "nom" est accessible.
        switch($status) {
            case $status == "0000":
                $response['message'] = "Pas assez de données n'ont été renseignées pour cet établissement";
                return $response;
                break;
            case $status == "0001":
                $response['message'] = "L'erp '".$nom."' sera conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee."."; 
                return $response;
                break;
            case $status == "0010":
                $response['message'] = "Le demandeur '".$erp['demandeur']."' s'est engagé à rendre l'erp '".$nom."' conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "0011":
                $response['message'] = "Le demandeur '".$erp['demandeur']."', a déclaré l'erp '".$nom."' conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "0100":
                $response['message'] = "L'erp '".$nom."' (".$erp['commune'].") sera conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "0101":
                $response['message'] = "L'erp '".$nom."' (".$erp['commune'].") s'est déclaré conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "0110":
                $response['message'] = "Le demandeur '".$erp['demandeur']."', s'est engagé à rendre l'erp '".$nom."', (".$erp['commune'].") conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "0111":
                $response['message'] = "Le demandeur '".$erp['demandeur']."', à déclaré l'erp '".$nom."', (".$erp['commune']."), comforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "1000":
                $response['message'] = "L'erp '".$nom."', (".$erp['adress']."), sera conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "1001":
                $response['message'] = "L'erp '".$nom."', (".$erp['adress']."), s'est déclaré conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "1010":
                $response['message'] = "Le demandeur '".$erp['demandeur']."' s'est engagé à rendre l'erp '".$nom."', (".$erp['adress']."), conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "1011":
                $response['message'] = "Le demandeur '".$erp['demandeur']."' a déclaré l'erp '".$nom."', (".$erp['adress']."), conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "1100":
                $response['message'] = "L'erp '".$nom."', (".$erp['adress']."), ".$erp['commune'].",  sera conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "1101":
                $response['message'] = "L'erp '".$nom."' (".$erp['adress']."), ".$erp['commune'].", a déclaré être conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
            case $status == "1110":
                $response['message'] = "Le demandeur '".$erp['demandeur']."' s'est engagé à rendre l'erp '".$nom."', (".$erp['adress']."), ".$erp['commune'].", conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap d'ici ".$mois." ".$annee.".";
                return $response;
                break;
            case $status == "1111":
                $response['message'] = "Le demandeur '".$erp['demandeur']."' a déclaré l'erp '".$nom."', (".$erp['adress']."), ".$erp['commune'].", conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                return $response;
                break;
        }
    }

    private function fillVariable($array) {
        $var = "";
        foreach($array as $keys) {
            !empty($keys)? $var.="1":$var.="0";
        }
        return $var;
    }
}
