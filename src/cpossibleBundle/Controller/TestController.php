<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestController extends AbstractController
{

    public function testAction()
    {

        $request = Request::createFromGlobals();

        $data = [];

        if (isset($_POST['adress'])) {

        $test = $_POST['adress'];

        $em = $this->getDoctrine()->getManager();
        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();

        for($i=0; $i <= sizeof($dbaListeerps) - 2; $i++){
            if ($dbaListeerps[$i]->getListeerpNumeroVoie() == '') {

                $data[] = array(
                    'name' => $dbaListeerps[$i]->getListeErpNomErp(),
                    'adress' => $dbaListeerps[$i]->getListeerpNomVoie(),
                    'type' => $dbaListeerps[$i]->getListeerpTypedossier(),
                    'demandeur' => $dbaListeerps[$i]->getListeerpDemandeur(),
                    'date' => $dbaListeerps[$i]->getListeerpDateValidAdap(),
                    'delai' => $dbaListeerps[$i]->getListeerpDelaiAdap()
                );

                $date_explode = explode('-',$data[$i]['date']);

                $date_explode[0] = $date_explode[0] + $data[$i]['delai'];

                $date = implode("-", $date_explode);

                if (isset($date_explode[1])) {

                    switch ($date_explode[1]) {
                        case "01":
                            $date = "Janvier" . " " . $date_explode[0];
                            break;
                        case "02":
                            $date = "Février" . " " . $date_explode[0];
                            break;
                        case "03":
                            $date = "Mars" . " " . $date_explode[0];
                            break;
                        case "04":
                            $date = "Avril" . " " . $date_explode[0];
                            break;
                        case "05":
                            $date = "Mai" . " " . $date_explode[0];
                            break;
                        case "06":
                            $date = "Juin" . " " . $date_explode[0];
                            break;
                        case "07":
                            $date = "Juillet" . " " . $date_explode[0];
                            break;
                        case "08":
                            $date = "Août" . " " . $date_explode[0];
                            break;
                        case "09":
                            $date = "Septembre" . " " . $date_explode[0];
                            break;
                        case "10":
                            $date = "Octobre" . " " . $date_explode[0];
                            break;
                        case "11":
                            $date = "Novembre" . " " . $date_explode[0];
                            break;
                        case "12":
                            $date = "Décembre" . " " . $date_explode[0];
                            break;
                    }

                }

                $data[$i]['date'] = $date;

            } else {

                $data[] = array(
                    'name' => $dbaListeerps[$i]->getlisteErpNomErp(),
                    'adress' => $dbaListeerps[$i]->getListeerpNumeroVoie() . ' ' . $dbaListeerps[$i]->getListeerpNomVoie(),
                    'type' => $dbaListeerps[$i]->getListeerpTypedossier(),
                    'demandeur' => $dbaListeerps[$i]->getListeerpDemandeur(),
                    'date' => $dbaListeerps[$i]->getListeerpDateValidAdap(),
                    'delai' => $dbaListeerps[$i]->getListeerpDelaiAdap()
                );

                $date_explode = explode('-',$data[$i]['date']);

                $date_explode[0] = $date_explode[0] + $data[$i]['delai'];

                $date = implode("-", $date_explode);

                if (isset($date_explode[1])) {

                    switch ($date_explode[1]) {
                        case "01":
                            $date = "Janvier" . " " . $date_explode[0];
                            break;
                        case "02":
                            $date = "Février" . " " . $date_explode[0];
                            break;
                        case "03":
                            $date = "Mars" . " " . $date_explode[0];
                            break;
                        case "04":
                            $date = "Avril" . " " . $date_explode[0];
                            break;
                        case "05":
                            $date = "Mai" . " " . $date_explode[0];
                            break;
                        case "06":
                            $date = "Juin" . " " . $date_explode[0];
                            break;
                        case "07":
                            $date = "Juillet" . " " . $date_explode[0];
                            break;
                        case "08":
                            $date = "Août" . " " . $date_explode[0];
                            break;
                        case "09":
                            $date = "Septembre" . " " . $date_explode[0];
                            break;
                        case "10":
                            $date = "Octobre" . " " . $date_explode[0];
                            break;
                        case "11":
                            $date = "Novembre" . " " . $date_explode[0];
                            break;
                        case "12":
                            $date = "Décembre" . " " . $date_explode[0];
                            break;
                    }

                }

                $data[$i]['date'] = $date;

            }
        }

        if($request->isMethod('post')) {

            $test = strstr($test,',', true);

            $use = explode(' ', $test);

            $number = $use[0];

            $use[0] = null;

            if (isset($use[1])) {

            switch ($use[1]) {
                case 'Allée':
                    $use[1] = 'ALL';
                    break;
                case 'Avenue':
                    $use[1] = 'AV';
                    break;
                case 'Boulevard':
                    $use[1] = 'BD';
                    break;
                case 'Carrefour':
                    $use[1] = 'CAR';
                    break;
                case 'Chemin':
                    $use[1] = 'CHE';
                    break;
                case 'Chaussée':
                    $use[1] = 'CHS';
                    break;
                case 'Cité':
                    $use[1] = 'CIT';
                    break;
                case 'Corniche':
                    $use[1] = 'COR';
                    break;
                case 'Cours':
                    $use[1] = 'CRS';
                    break;
                case 'Domaine':
                    $use[1] = 'DOM';
                    break;
                case 'Descente':
                    $use[1] = 'DSC';
                    break;
                case 'Ecart':
                    $use[1] = 'ECA';
                    break;
                case 'Esplanade':
                    $use[1] = 'ESP';
                    break;
                case 'Faubourg':
                    $use[1] = 'FG';
                    break;
                case 'Grand Rue':
                    $use[1] = 'GR';
                    break;
                case 'Hameau':
                    $use[1] = 'HAM';
                    break;
                case 'Halle':
                    $use[1] = 'HLE';
                    break;
                case 'Impasse':
                    $use[1] = 'IMP';
                    break;
                case 'Lieu-dit':
                    $use[1] = 'LD';
                    break;
                case 'Lotissement':
                    $use[1] = 'L';
                    break;
                case 'Marché':
                    $use[1] = 'MAR';
                    break;
                case 'Montée':
                    $use[1] = 'MTE';
                    break;
                case 'Passage':
                    $use[1] = 'PAS';
                    break;
                case 'Place':
                    $use[1] = 'PL';
                    break;
                case 'Plaine':
                    $use[1] = 'PLN';
                    break;
                case 'Plateau':
                    $use[1] = 'PLT';
                    break;
                case 'Promenade':
                    $use[1] = 'PRO';
                    break;
                case 'Parvis':
                    $use[1] = 'PRV';
                    break;
                case 'Quartier':
                    $use[1] = 'QUA';
                    break;
                case 'Quai':
                    $use[1] = 'QUA';
                    break;
                case 'Résidence':
                    $use[1] = 'RES';
                    break;
                case 'Ruelle':
                    $use[1] = 'RLE';
                    break;
                case 'Rocade':
                    $use[1] = 'ROC';
                    break;
                case 'Rond-point':
                    $use[1] = 'RPT';
                    break;
                case 'Route':
                    $use[1] = 'RTE';
                    break;
                case 'Rue':
                    $use[1] = 'R';
                    break;
                case 'Sente':
                    $use[1] = 'SEN';
                    break;
                case 'Sentier':
                    $use[1] = 'SEN';
                    break;
                case 'Square':
                    $use[1] = 'SQ';
                    break;
                case 'Terre-plein':
                    $use[1] = 'TPL';
                    break;
                case 'Traverse':
                    $use[1] = 'TRA';
                    break;
                case 'Villa':
                    $use[1] = 'VLA';
                    break;
                case 'Village':
                    $use[1] = 'VLG';
                    break;
            }

            }

            $use = implode(' ', $use);
            $use = str_replace('é', 'e', $use);
            $use = str_replace('è', 'e', $use);
            $use = str_replace('à', 'a', $use);
            $use = str_replace('ï', 'i', $use);
            $use = str_replace('î', 'i', $use);
            $use = str_replace('â', 'a', $use);
            $use = str_replace('ô', 'ô', $use);
            $use = str_replace('û', 'û', $use);
            $use = str_replace('ê', 'ê', $use);
            $use = str_replace('ç', 'c', $use);
            $use = str_replace('ù', 'u', $use);
            $use = preg_replace('/ /', '', $use, 1);
            $use = mb_strtoupper($use);

            $use = $number . ' ' . $use;

            /*

            echo "<pre>";
            var_dump($use);
            echo "</pre>";

            */

            $o = 0;

            foreach($data as $result) {

                if ($result['adress'] == $use) {

                    $res[] = $result;

                    $o++;

                }

            }

            if ($o == 1) {

                $response = new JsonResponse();
                $response->setData($res);
                return $response;

            } else {

                if (isset($res)) {

                $response = new JsonResponse();
                $response->setData($res);
                return $response;

                }


            }

        }

        if (isset($response)) {

        return $this->render('cpossibleBundle:Test:test.html.twig', array('response' => $response));

        }

        }

        return $this->render('cpossibleBundle:Test:test.html.twig');

    }

}