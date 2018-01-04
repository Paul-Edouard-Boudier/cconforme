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
        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
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
        $longmin = $lng - 0.05;
        $longmax = $lng + 0.05;
        $latmin = $lat - 0.05;
        $latmax = $lat + 0.05;

        //dump($request);die;
        // Query looks like that:
        // Select all from listeerp where lat < lat + 0.05 && lat > lat - 0.05 && lng < lng + 0.05 && lng > lng +0.05
        $conn = $this->getDoctrine()->getManager()
                      ->getConnection();
        //$sql = "SELECT * FROM articles WHERE id = ? LIMIT 6";
        $sql = "SELECT listeERP_latitude, listeERP_longitude FROM resicadminresic.dba_listeERP
          WHERE listeERP_longitude > ?
          AND listeERP_longitude < ?
          AND listeERP_latitude > ?
          AND listeERP_latitude < ?
          LIMIT 6;";
        $stmt = $conn->prepare($sql);
        // find a way to bind everything at the same time, cause it's ugly
        $stmt->bindValue(1, $longmin);
        $stmt->bindValue(2, $longmax);
        $stmt->bindValue(3, $latmin);
        $stmt->bindValue(4, $latmax);
        $stmt->execute();
        // $markers = [];
        $actualLocation = ['listeERP_latitude' => $lat, 'listeERP_longitude' => $lng];
        //array_push($markers, $actualLocation);
        $result = $stmt->fetchAll();
        array_push($result, $actualLocation);

        return new JsonResponse($result);
        //return new JsonResponse($request);
      } else {
        return "Failed";
      }
      // dump($lat);die;
      //return $this->render('cpossibleBundle:Home:accueil.html.twig');
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

        return $this->render('cpossibleBundle:Home:accueil.html.twig');

    }

    /**
     * @param $status 'ok' | 'ko'
     * @param null $erp ErpEntity
     * @return array
     */
    private function constructResponseMessage($status, $erp = null) {
      // setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
      // $date = $erp["date"];
      // $delai = $erp["delai"];
      // //
      // $date = new DateTime($date);
      // $mois = ucfirst(strftime("%B", $date->getTimestamp()));
      // //
      // $date->add(new DateInterval('P'.$delai.'Y'));
      // $newdate =  $date->format('d-m-Y');
      //
      // $time = strtotime($newdate);
      // $annee = date("Y",$time);
        $messageText = [
            'standard' => "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, ",
            'pending' => "L'établissement situé à cette adresse a déclaré être rentrés dans la démarche de mise en accessibilité.",
            'none' => "Aucun établissement situé à cette adresse n’a déclaré être rentré dans la démarche de mise en accessibilité."
        ];

        switch($status){
            case 'ok':
                $response['status'] = 'ok';
                if ($erp['type'] == 'adap' or 'at-adap') {
                    $response["message"] = $messageText['pending'] . " Le demandeur " . $erp["demandeur"] .
                        " s’est engagé à rendre l’ERP " . $erp["name"] . ", situé au " . $erp["adress"] .
                        " conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap avant " .
                        $erp["date"] . ".";
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

    /**
     * @param $erpRepo 'ErpRepository'
     * @param $param array
     * @return 'ErpEntityArray'
     */
    private function getErpEntity($erpRepo, $param) {
        if ($erpEntity = $erpRepo->findOneBy($param)) {
            $erp = [
                'name' => $erpEntity->getListeErpNomErp(),
                'adress' => $erpEntity->getListeerpNomVoie(),
                'type' => $erpEntity->getListeerpTypedossier(),
                'demandeur' => $erpEntity->getListeerpDemandeur(),
                'date' => $erpEntity->getListeerpDateValidAdap(),
                'delai' => $erpEntity->getListeerpDelaiAdap(),
            ];
            if ($erpEntity->getListeerpNumeroVoie() != '') $erp['adress'] = $erpEntity->
                getListeerpNumeroVoie() . ' ' . $erpEntity->getListeerpNomVoie();
            return $erp;
        }else {
            return false;
        }
    }
}
