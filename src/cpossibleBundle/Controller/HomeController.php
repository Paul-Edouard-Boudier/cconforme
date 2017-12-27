<?php

namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            'pending' => "l'établissement situé à cette adresse a déclaré être rentrés dans la démarche de mise en accessibilité.",
            'none' => "aucun établissement situé à cette adresse n’a déclaré être rentré dans la démarche de mise en accessibilité."
        ];

        switch($status){
            case 'ok':
                $response['status'] = 'ok';
                if ($erp['type'] == 'adap') {
                    $response["message"] = $messageText['standard'] . $messageText['pending'] . " Le demandeur " . $erp["demandeur"] .
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
                    "message" => $messageText['standard'] . $messageText['none'],
                    "status" => 'ko'
                ];
            default:
                return;
        }
    }
}
