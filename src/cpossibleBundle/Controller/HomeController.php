<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
        ));
    }

    public function fetchAction ()
    {
        $data = [];

        $success = "";
        $error2 = "";

        if(isset($_POST['name'])){

            $name= $_POST['name'];
            $adress = $_POST['adress'];

            $form = array(
                'name' => $name,
                'adress' => $adress,
            );

            $em = $this->getDoctrine()->getManager(); // on récup la totalité de la DB et on le stock dans une variable $em (entity manager)

            $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll(); // on choisi une entité afin de recup sa table en DB.



            for($i=0; $i <= sizeof($dbaListeerps)-1; $i++){
                if ($dbaListeerps[$i]->getListeerpNumeroVoie() == '') {
                    $data[] = array(
                      'name' => $dbaListeerps[$i]->getListeErpNomErp(),
                      'adress' =>$dbaListeerps[$i]->getListeerpNomVoie(),
                    );
                } else {
                    $data[] = array(
                        'name' => $dbaListeerps[$i]->getlisteErpNomErp(),
                        'adress' =>$dbaListeerps[$i]->getListeerpNumeroVoie() . ' ' . $dbaListeerps[$i]->getListeerpNomVoie(),
                    );
                }
            }

            /*var_dump($form);

            echo "<pre>";
            var_dump($data);
            echo "</pre>";
*/
            $error = '0';

            foreach ($data as $result) {
                if ($form['name'] == "") {
                    $error = -1;
                    break;
                } elseif ($result['name'] == $form['name']) {
                    $error = 0;
                    break;
                } else {
                    $error++;
                }
            }

            if ($error == 0) {
                $success = "Cet ERP est conforme";
                $error2 = NULL;
            }elseif ($error == -1) {

            } else {
                $error2 = "Cet ERP n'est pas conforme";
                $success = NULL;
            }

            foreach ($data as $result) {
                if ($form['adress'] == "") {
                    $error = -1;
                    break;
                } elseif ($result['adress'] == $form['adress']) {
                    $error = 0;
                    break;
                } else {
                    $error++;
                }
            }

            if ($error == 0) {
                $success = "Cet ERP est conforme";
                $error2 = NULL;
            } elseif ($error == -1) {

            } else {
                $error2 = "Cet ERP n'est pas conforme";
                $success = NULL;
            }

        } else{

        }

        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'success' => $success,
            'error2' => $error2,
        )); // retour de la vue
    }
}
