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

        $test = "";

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
                echo 'ok <br>';
            }elseif ($error == -1) {

            } else {
                echo 'error <br>';
            }

            foreach ($data as $result) {
                if ($form['adress'] == "") {
                    $error = -1;
                    $test = 'test';
                    break;
                } elseif ($result['adress'] == $form['adress']) {
                    $error = 0;
                    break;
                } else {
                    $error++;
                }
            }

            if ($error == 0) {
                echo 'ok';
            } elseif ($error == -1) {

            } else {
                echo 'error';
            }

        } else{

        }

        return $this->render('cpossibleBundle:Home:accueil.html.twig', array('test' => $test)); // retour de la vue
    }
}
