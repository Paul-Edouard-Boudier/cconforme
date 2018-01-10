<?php

namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Datetime;
use \DateInterval;

class HomeController extends AbstractErpController
{
    public function accueilAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
        $typesErp = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
        dump($typesErp);die;
        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
            'typesErp' => $typesErp,
        ));
    }

    public function showMarkersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();

        return $this->render('cpossibleBundle:Home:map.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
        ));
    }

    public function aroundAction(Request $request) {
      if ($request->isXMLHttpRequest()) {
        $lat = floatval($request->get('lat'));
        $lng = floatval($request->get('lng'));
        // limit = number of items i want to display
        $limit = intval($request->get('limit'));

        $conn = $this->getDoctrine()->getManager()
                      ->getConnection();
        $sql = "SELECT listeERP_latitude, listeERP_longitude, liste_ERP_nom_erp FROM resicadminresic.dba_listeERP;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $actualLocation = ['listeERP_latitude' => $lat, 'listeERP_longitude' => $lng, 'liste_ERP_nom_erp' => 'Vous êtes ici'];

        // we could set this dynamically aswell
        // the distance from the point where we are actually seraching items
        $rayon = 0.002;
        $markers = []; // The array that hold every single item retrieved by query
        // Here we calculate the distance for each point to the center
        // that is the point where the user is searching
        // and then we push into [markers], every items that we want
        foreach ($result as $item) {
          $ilat = floatval($item['listeERP_latitude']);
          $ilong = floatval($item['listeERP_longitude']);
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
    public function search_listAction(Request $request) {
      if ($request->isXMLHttpRequest()) {
        //dump($request);die;
        //dump($request->get('type'));die;
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
        $queryBuilder
            ->andWhere('dba.listeerpNomCommune LIKE :Commune')
            ->andWhere('dba.listeErpNomErp LIKE :Nom')
            ->setParameter('Commune', '%' . $request->get('commune') . '%' )
            ->setParameter('Nom', '%' . $request->get('nom') . '%');
        if ($request->get('type') !== 'null') {
          //dump(true);die;
          $queryBuilder->andWhere('dba.listeerpType LIKE :Type')
            ->setParameter('Type', '%' . $request->get('type') . '%');
        }
        $result = $queryBuilder->getQuery();
        $erps = $result->getArrayResult();
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

        if($request->isMethod('post') &&
            $search->get('data')
        ) {
            if($response = $this->getSingleErp($search->get('data'))) {
              if ($response != null) {
                $response = $this->parseErpEntity($response);
                $response = $this->constructResponseMessage('ok', $response);
              }
            } else {
                $response = $this->constructResponseMessage('ko');
            };

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
     * @param $status 'ok' | 'ko'
     * @param null $erp ErpEntity
     * @return array
     */
    private function constructResponseMessage($status, $erp = null) {
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        $date = $erp["date"];
        $delai = $erp["delai"];
        //
        $date = new DateTime($date);
        $mois = ucfirst(strftime("%B", $date->getTimestamp()));
        //
        $date->add(new DateInterval('P'.$delai.'Y'));
        $newdate =  $date->format('d-m-Y');
        $time = strtotime($newdate);
        $annee = date("Y",$time);
        $messageText = [
            'standard' => "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, ",
            'pending' => "L'établissement situé à cette adresse a déclaré être rentrés dans la démarche de mise en accessibilité.",
            'none' => "Aucun établissement situé à cette adresse n’a déclaré être rentré dans la démarche de mise en accessibilité."
        ];
        switch($status){
            case 'ok':
                $response['status'] = 'ok';
                if ($erp['type'] == 'adap') {
                    $response["message"] = $messageText['pending'] . " Le demandeur " . $erp["demandeur"] .
                        " s’est engagé à rendre l’ERP " . $erp["name"] . ", situé au " . $erp["adress"] .
                        " conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap avant " .
                        $mois . " " .$annee. ".";
                } else {
                    $response["message"] = "Le demandeur " . $erp["demandeur"] . "a déclaré l’établissement " .
                        $erp['name'] . ", situé au " . $erp["adress"] .
                        " conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap.";
                }
                // TODO create method getCloseErpFromGeo
                return $response;
            case 'ko':
                return [
                    "message" => $messageText['none'],
                    "status" => 'ko'
                ];
            default:
                return;
        }
    }
}
