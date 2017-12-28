<?php

namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
                $response = $this->parseErpEntity($response);
                $response = $this->constructResponseMessage('ok', $response);
            } else {
                $response = $this->constructResponseMessage('ko');
            };

            $res = new JsonResponse();
            $res->setData($response);
            return $res;
        }

        return $this->render('cpossibleBundle:Home:accueil.html.twig');



        /*

        $data = [];
        $success = "";
        $success2 = "";
        $error2 = "";

        $name= $_POST['name'];
        $adress = $_POST['adress'];

        $form = array(
            'name' => $name,
            'adress' => $adress,
        );

         // on récup la totalité de la DB et on le stock dans une variable $em (entity manager)

        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll(); // on choisi une entité afin de recup sa table en DB.



        for($i=0; $i <= sizeof($dbaListeerps)-1; $i++){
            if ($dbaListeerps[$i]->getListeerpNumeroVoie() == '') {
                $data[] = array(
                  'name' => $dbaListeerps[$i]->getListeErpNomErp(),
                  'adress' => $dbaListeerps[$i]->getListeerpNomVoie(),
                  'type' => $dbaListeerps[$i]->getListeerpTypedossier(),
                  'demandeur' => $dbaListeerps[$i]->getListeerpDemandeur(),
                  'date' => $dbaListeerps[$i]->getListeerpDateValidAdap(),
                );
            } else {
                $data[] = array(
                    'name' => $dbaListeerps[$i]->getlisteErpNomErp(),
                    'adress' => $dbaListeerps[$i]->getListeerpNumeroVoie() . ' ' . $dbaListeerps[$i]->getListeerpNomVoie(),
                    'type' => $dbaListeerps[$i]->getListeerpTypedossier(),
                    'demandeur' => $dbaListeerps[$i]->getListeerpDemandeur(),
                    'date' => $dbaListeerps[$i]->getListeerpDateValidAdap(),
                );
            }
        }

        var_dump($form);

        echo "<pre>";
        var_dump($data);
        echo "</pre>";

        $error = '0';

        foreach ($data as $result) {
            if ($result['name'] == $form['name']) {
                $data = $result;
                break;
            }
        }

        if ($error == 0) {
            $success = "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, cet établissement à déclaré être
            rentrés dans la démarche de mise en accessibilité.";
            $error2 = NULL;
            if ($data['type'] == 'adap') {
                $success2 = $success . " Le demandeur " . $data["demandeur"] . " s’est engagé à rendre l’ERP "  . $data["name"] . ",
                situé au " . $data["adress"] ." conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap
                avant le " . $data["date"] . "." ;
                $success = NULL;
            } else {
                $success = "Le demandeur " . $data["demandeur"] . "a déclaré l’établissement " . $data['name'] . ", situé au " . $data["adress"] . "
                conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap ";
            }
        }elseif ($error == -1) {

        } else {
            $error2 = "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, aucun établissement situé à cette
            adresse n’a déclaré être rentré dans la démarche de mise en accessibilité";
            $success = NULL;
        }

        foreach ($data as $result) {
            if ($form['adress'] == "") {
                $error = -1;
                break;
            } elseif ($result['adress'] == $form['adress']) {
                $error = 0;
                $data = $result;
                break;
            } else {
                $error++;
            }
        }

        if ($error == 0) {
            $success = "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, l'établissement situé à
            cette adresse à déclaré être rentrés dans la démarche de mise en accessibilité.";
            $error2 = NULL;
            if ($data['type'] == 'adap' or 'at-adap') {
                $success2 = $success . " Le demandeur " . $data["demandeur"] . " s’est engagé à rendre l’ERP "  . $data["name"] . ",
                situé au " . $data["adress"] ." conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap
                avant le " . $data["date"] . ".";
                $success = NULL;
            } else {
                $success = "Le demandeur " . $data["demandeur"] . "a déclaré l’établissement " . $data['name'] . ", situé au " . $data["adress"] . "
                conforme à la réglementation en matière d’accessibilité des personnes en situation de handicap ";
            }
        } elseif ($error == -1) {

        } else {
            $error2 = "Sauf erreur et en prenant en compte les précautions d’usage listées ci-dessus, aucun établissement situé à cette
            adresse n’a déclaré être rentré dans la démarche de mise en accessibilité";
            $success = NULL;
        }

        $data = array(
            'success' => $success,
            'success2' => $success2,
            'error2' => $error2,
        );

        $response = new JsonResponse();
        $response->setData($data);

        return $response;

    }



    return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
        'success' => $success,
        'success2' => $success2,
        'error2' => $error2,
    ));*/

    }

    /**
     * @param $status 'ok' | 'ko'
     * @param null $erp ErpEntity
     * @return array
     */
    private function constructResponseMessage($status, $erp = null) {
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
}
