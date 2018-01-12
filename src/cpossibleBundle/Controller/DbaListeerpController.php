<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\DbaListeerp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Unirest;




/**
 * Dbalisteerp controller.
 *
 */
class DbaListeerpController extends Controller
{

    /**
     * Lists all dbaListeerp entities.
     *
     */

    /**
     * @Route("/liste/index", name="list")
     */
    public function indexAction(Request $request)
    {
      if (session_status() == PHP_SESSION_NONE) {
          session_start();
      }


        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {


                $em = $this->getDoctrine()->getManager();

                $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
                $_SESSION['request'] = [];
                if($request->query->getAlnum('adap')){
                  $_SESSION['request']['adap'] = $request->query->getAlnum('adap');
                    $queryBuilder
                        ->andWhere('dba.listeerpIdAdap LIKE :listeerpIdAdap')
                        ->setParameter('listeerpIdAdap', '%' . $request->query->getAlnum('adap') . '%' );
                }

                if($request->query->getAlnum('commune')){
                  $_SESSION['request']['commune'] = $request->query->getAlnum('commune');
                    $queryBuilder
                        ->andWhere('dba.listeerpNomCommune LIKE :listeerpNomCommune')
                        ->setParameter('listeerpNomCommune', '%' . $request->query->getAlnum('commune') . '%' );
                }

                if($request->query->getAlnum('demandeur')){
                  $_SESSION['request']['demandeur'] = $request->query->getAlnum('demandeur');
                    $queryBuilder
                        ->andWhere('dba.listeerpDemandeur LIKE :listeerpDemandeur')
                        ->setParameter('listeerpDemandeur', '%' . $request->query->getAlnum('demandeur') . '%' );
                }

                if($request->query->getAlnum('nom_erp')){
                  $_SESSION['request']['nom_erp'] = $request->query->getAlnum('nom_erp');
                    $queryBuilder
                        ->andWhere('dba.listeErpNomErp LIKE :listeErpNomErp')
                        ->setParameter('listeErpNomErp', '%' . $request->query->getAlnum('nom_erp') . '%' );
                }

                if($request->query->getAlnum('nom_voie')){
                  $_SESSION['request']['nom_voie'] = $request->query->getAlnum('nom_voie');
                    $queryBuilder
                        ->andWhere('dba.listeerpNomVoie LIKE :listeerpNomVoie')
                        ->setParameter('listeerpNomVoie', '%' . $request->query->getAlnum('nom_voie') . '%' );
                }

                if($request->query->getAlnum('siret')){
                  $_SESSION['request']['siret'] = $request->query->getAlnum('siret');
                    $queryBuilder
                        ->andWhere('dba.listeerpSiret LIKE :listeerpSiret')
                        ->setParameter('listeerpSiret', '%' . $request->query->getAlnum('siret') . '%' );
                }

                if($request->query->getAlnum('lng')){
                  $_SESSION['request']['lng'] = $request->query->getAlnum('lng');
                    $queryBuilder
                        ->andWhere('dba.listeerpLongitude LIKE :listeerpLongitude')
                        ->setParameter('listeerpLongitude', '%' . $request->query->getAlnum('lng') . '%' );
                }
                if($request->query->getAlnum('lat')){
                  $_SESSION['request']['lat'] = $request->query->getAlnum('lat');
                    $queryBuilder
                        ->andWhere('dba.listeerpLatitude LIKE :listeerpLatitude')
                        ->setParameter('listeerpLatitude', '%' . $request->query->getAlnum('lat') . '%' );
                }

                $dbaListeerps = $queryBuilder->getQuery();

                $paginator = $this->get('knp_paginator');


                $result =$paginator->paginate(
                    $dbaListeerps,
                    $request->query->getInt('page', 1),
                    $request->query->getInt('limit', 10)
                );

                return $this->render('dbalisteerp/index.html.twig', array(
                    'dbaListeerps' => $result,
                ));


            } else {

                return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');

            }

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * @Route("/liste/index/csv", name="export")
     */
    public function exportAction(Request $request){

      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

          if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
            if(!empty($_SESSION['request'])) {
              if(array_key_exists('adap', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeerpIdAdap LIKE :listeerpIdAdap')
                      ->setParameter('listeerpIdAdap', '%' . $_SESSION['request']['adap'] . '%' );
              }

              if(array_key_exists('commune', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeerpNomCommune LIKE :listeerpNomCommune')
                      ->setParameter('listeerpNomCommune', '%' . $_SESSION['request']['commune'] . '%' );
              }

              if(array_key_exists('demandeur', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeerpDemandeur LIKE :listeerpDemandeur')
                      ->setParameter('listeerpDemandeur', '%' . $_SESSION['request']['demandeur'] . '%' );
              }

              if(array_key_exists('nom_erp', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeErpNomErp LIKE :listeErpNomErp')
                      ->setParameter('listeErpNomErp', '%' . $_SESSION['request']['nom_erp'] . '%' );
              }

              if(array_key_exists('nom_voie', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeerpNomVoie LIKE :listeerpNomVoie')
                      ->setParameter('listeerpNomVoie', '%' . $_SESSION['request']['nom_voie'] . '%' );
              }

              if(array_key_exists('siret', $_SESSION['request']) == true){
                  $queryBuilder
                      ->andWhere('dba.listeerpSiret LIKE :listeerpSiret')
                      ->setParameter('listeerpSiret', '%' . $_SESSION['request']['siret'] . '%' );
              }
            }


            $dbaListeerps = $queryBuilder->getQuery();
            $paginator = $this->get('knp_paginator');


            $result =$paginator->paginate(
                $dbaListeerps,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 20000)
            );


            $csv = $result;

            $rows = array();

            foreach ($csv as $event) {
                $data = array($event->getListeerpDemandeur(), $event->getListeErpNomErp());

                $rows[] = implode(',', $data);
            }

            $content = implode("\n", $rows);
            $response = new Response($content);
            $response->headers->set('Content-Type', 'text/csv');

            return $this->render('dbalisteerp/csv.html.twig', array(
                'dbaListeerps' => $result,
            ), $response);
          } else {

          return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');

        }

      } else {

        return $this->redirectToRoute('fos_user_security_login');
      }
    }
    /**
     * Creates a new dbaListeerp entity.
     *
     */
    public function newAction(Request $request)
    {

        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {

                $dbaListeerp = new Dbalisteerp();
                $form = $this->createForm('cpossibleBundle\Form\DbaListeerpType', $dbaListeerp);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                  $types = "";
                  foreach ($dbaListeerp->getListeerpType() as $key => $type) {
                    $types .= $type.",";
                  }
                  $dbaListeerp->setListeerpType($types);
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($dbaListeerp);
                  $em->flush();
                  return $this->redirectToRoute('dbalisteerp_show', array('listeerpId' => $dbaListeerp->getListeerpid()));
                }

                return $this->render('dbalisteerp/new.html.twig', array(
                    'dbaListeerp' => $dbaListeerp,
                    'form' => $form->createView(),
                ));

            } else {

                return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');

            }

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * Finds and displays a dbaListeerp entity.
     *
     */
    public function showAction(DbaListeerp $dbaListeerp)
    {

        $deleteForm = $this->createDeleteForm($dbaListeerp);

        return $this->render('dbalisteerp/show.html.twig', array(
            'dbaListeerp' => $dbaListeerp,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing dbaListeerp entity.
     *
     */
    public function editAction(Request $request, DbaListeerp $dbaListeerp)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {

                $deleteForm = $this->createDeleteForm($dbaListeerp);
                $editForm = $this->createForm('cpossibleBundle\Form\DbaListeerpType', $dbaListeerp);
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirectToRoute('dbalisteerp_show', array('listeerpId' => $dbaListeerp->getListeerpid()));
                }

                return $this->render('dbalisteerp/edit.html.twig', array(
                    'dbaListeerp' => $dbaListeerp,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));

            } else {

                return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');

            }

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * Deletes a dbaListeerp entity.
     *
     */
    public function deleteAction(Request $request, DbaListeerp $dbaListeerp)
    {

        $form = $this->createDeleteForm($dbaListeerp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dbaListeerp);
            $em->flush();
        }

        return $this->redirectToRoute('dbalisteerp_index');
    }

    /**
     * Creates a form to delete a dbaListeerp entity.
     *
     * @param DbaListeerp $dbaListeerp The dbaListeerp entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DbaListeerp $dbaListeerp)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dbalisteerp_delete', array('listeerpId' => $dbaListeerp->getListeerpid())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * Function that will retrieve infos from the tps procedure and all the dossiers it has
     * and then update the database if the element doesn't exist already
     */
    public function tpsAction() {
      echo '<pre>';
      //$procedure = $response->body->procedure;
      // On peut très bien faire une requete de procédure dynamique (donc en checkant le dpt par ex)
      // Et faire une requete de dossier pour chaque dossier trouvé
      $procedure = '2004';
      $token = '85cc86ebbca4d1b518db1f597256b365df4465de';

      // On récupère tous les dossiers d'une procédure (donc tous les dossier de chaque bâtiment)
      // Puis on fait une deuxième requete pour récupérer les infos pour chaque dossier trouvé
      $response = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers?token='.$token.'');
      $dossiers =$response->body->dossiers;
      $em = $this->getDoctrine()->getManager();
      foreach ($dossiers as $dossier) {
        $subResponse = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers/'.$dossier->id.'?token='.$token.'');
        $entity = $subResponse->body->dossier;
        $erp = new Dbalisteerp();

        // Here's how we get the entries
        foreach ($entity->champs as $champ) {
          $libelle = $champ->type_de_champ->libelle;
          $value = $champ->value;
          // /!\ CURRENTLY WORKING /!\

          // if ($libelle == 'Département') {
          //   // Regex to get the first 1 to 3 digits that hold the dpt number
          //   $selector = "/[0-9]{1,3}/";
          //   preg_match($selector, $value, $departement);
          //   // dump($departement[0]);
          //   $erp->setListeerpDepartement($departement[0]);
          // }
          // if ($libelle == "Type d'établissement") {
          //   $nature = strtolower($value);
          //   // dump($nature);
          //   $erp->setListeerpNature($nature);
          // }
          // if ($libelle == "Catégorie") {
          //   $selector = "/[0-9]{1,2}/";
          //   preg_match($selector, $value, $categorie);
          //   // dump($categorie[0]);
          //   $erp->setListeerpCategorie($categorie[0]);
          // }
          // if ($libelle == "Date de mise en conformité") {
          //   // Not sure if it's the right date
          //   // dump($value);
          //   $erp->setListeerpDateValidAdap($value);
          // }
          // if ($libelle == "Durée de la dérogation en année") {
          //   if (!empty($value)) {
          //     // dump($value);
          //     $erp->setListeerpDelaiAdap($value);
          //   }
          // }
          // if ($libelle == "Numéro d'adap") {
          //   if (!empty($value)) {
          //     // dump($value);
          //     $erp->setListeerpIdAdap($value);
          //   }
          // }
          // if ($libelle == "Nom de l'établissement, enseigne") {
          //   // dump($value);
          //   $erp->setListeErpNomErp($value);
          // }
          // if ($libelle == "Siret") {
          //   if (empty($value)) {
          //     // dump($value);
          //     $erp->setListeerpSiret(0);
          //   }
          //   else {
          //     // dump(intval($value));
          //     $erp->setListeerpSiret(intval($value));
          //   }
          // }
          // if ($libelle == "Type de déclaration") {
          //   //dump(strtolower($value));
          //   $erp->setListeerpTypedossier(strtolower($value));
          // }
          // if ($libelle == "Types d'activités") {
          //   // select all character of $value between A-Z followed by whitespace
          //   $selector = "/[A-Z]{1,3}\s/";
          //   preg_match_all($selector, $value, $typesUgly);
          //   // delete whitespace from string that the previous regex returns us
          //   $typesBeautified = preg_replace('/\s+/', '', $typesUgly[0]);
          //   $types = "";
          //   $last = count($typesBeautified);
          //   $i = 0;
          //   foreach ($typesBeautified as $type) {
          //     if ($i == $last - 1) {
          //       $types .= $type;
          //     }
          //     else {
          //       $types .= $type.',';
          //     }
          //     $i ++;
          //   }
          //   // dump($types);
          //   $erp->setListeerpType($types);
          // }
          // if ($libelle == "Adresse") {
          //   // Regex that split a string like this:
          //   // 11 Place de l'Europe 69006 Lyon
          //   // into an array like this:
          //   // ["full_adress_here","11", "Place de l'Europe", "69006", "Lyon"];
          //   $selector = "/(\d{1,4})\s?([a-zA-ZÀ-Ÿ',\s]*\s?)?\s?([0-9]{5})\s?([a-zA-ZÀ-Ÿ',\/]*\s*)/";
          //   preg_match($selector, $value, $splitedAdress);
          //   $numero_de_voie = $splitedAdress[1];
          //   $erp->setListeerpNumeroVoie($numero_de_voie);
          //   // // Adress that we need to write like "PL DE L'EUROPE"
          //   $tempAdress = $splitedAdress[2]; // Here: "Place de l'Europe "
          //   $adressExploded = explode(" ",$tempAdress);
          //   $intitule_voie = $adressExploded[0]; // "Place"
          //
          //   // We search in ddb the "intitulevoie" that match with what we get from the "dossier"
          //   $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
          //   $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
          //     ->setParameter('intitulevoieNom', '%' . $intitule_voie . '%' );
          //   $result = $q->getQuery();
          //   // Here we want to get the name of voie as we wish to put in ddb like "PL"
          //   $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
          //   $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
          //   $fulladress = "";
          //   $fulladress .= $voie;
          //   for ($i=1; $i < count($adressExploded) ; $i++) {
          //     $fulladress .= " " .strtoupper($adressExploded[$i]);
          //   }
          //   // supress the last whitespace form the stirng
          //   $erp->setListeerpNomVoie(rtrim($fulladress));
          //
          //   $codePostal = $splitedAdress[3];
          //   $erp->setListeerpCodePostal($codePostal);

          // TODO: retrieve adresse and ask googlemap api to have the commune + long and lat
          // }
          // if ($libelle == "Nom de l'entreprise") {
          //   $erp->setListeerpDemandeur($value);
          // }
          // // For now status is always 0:
          // $erp->setListeerpStatut(0);
          // // /!\ END CURRENTLY WORKING /!\

          // $em->persist($erp);
          // $em->flush();
        }
        dump($erp);
      }
      dump('fin');die;
      return $this->render('cpossibleBundle:TPS:index.html.twig', ['procedure' => $procedure]);
    }

    // private function alreadyExist() {
    //   $em = $this->getDoctrine()->getManager();
    //   $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
    //   $queryBuilder
    //       ->andWhere('dba.listeErpNomErp LIKE :listeErpNomErp')
    //       ->setParameter('listeErpNomErp', '%' . $_SESSION['request']['siret'] . '%' );
    // }
    //
    //
    // $dbaListeerps = $queryBuilder->getQuery();

}
