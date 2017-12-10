<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function fetchAction ()
    {
        $data = [];

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
                $data[] = array(
                    'name' => $dbaListeerps[$i]->getlisteErpNomErp(),
                    'adress' =>$dbaListeerps[$i]->getListeerpNumeroVoie() . ' ' . $dbaListeerps[$i]->getListeerpNomVoie(),
                );
            }

            /*var_dump($form);

            echo "<pre>";
            var_dump($data);
            echo "</pre>";
 */

            $error = '0';
            private $success;
            private $error2;

            foreach ($data as $result) {
                if ($result['name'] == $form['name']) {
                    $error = 0;
                    break;
                } else {
                    $error++;
                }

            }

            foreach ($data as $result) {
                if ($result['adress'] == $form['adress']) {
                    $error = 0;
                    break;
                } else {
                    $error++;
                }

            }

            if ($error == 0) {
                echo $success;
            } else {
                echo $error2;
            }


        } else{
            return $this->render('cpossibleBundle:Home:accueil.html.twig', array('success' => , 'error2' =>));
        }

        return $this->render('cpossibleBundle:Home:accueil.html.twig'); // retour de la vue
    }
}
