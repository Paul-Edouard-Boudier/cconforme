<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\DbaListeerp;
use cpossibleBundle\Entity\DbaTypeactivite;
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

        if ($securityContext->isGranted('ROLE_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
                $_SESSION['request'] = [];
            if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                if ($this->getUser()->getCommune()) {
                  $commune = $this->getUser()->getCommune();
                  $_SESSION['request']['commune'] = $commune;
                  $queryBuilder
                        ->andWhere('dba.listeerpNomCommune LIKE :listeerpNomCommune')
                        ->setParameter('listeerpNomCommune', $commune );
                }
                $dpt = $this->getUser()->getDepartement();
                $_SESSION['request']['departement'] = $dpt;
                $queryBuilder->andWhere('dba.listeerpDepartement LIKE :dpt')
                ->setParameter('dpt', $dpt);
            }
                if(isset($_GET['adap'])){
                  $_SESSION['request']['adap'] = $_GET['adap'];
                    $queryBuilder
                        ->andWhere('dba.listeerpIdAdap LIKE :listeerpIdAdap')
                        ->setParameter('listeerpIdAdap', '%' . $_GET['adap'] . '%' );
                }

                if(isset($_GET['commune']) && !$_SESSION['request']['commune']){
                  $_SESSION['request']['commune'] = $_GET['commune'];
                    $queryBuilder
                        ->andWhere('dba.listeerpNomCommune LIKE :listeerpNomCommune')
                        ->setParameter('listeerpNomCommune', '%' . $_GET['commune'] . '%' );
                }

                if(isset($_GET['demandeur'])){
                  $_SESSION['request']['demandeur'] = $_GET['demandeur'];
                    $queryBuilder
                        ->andWhere('dba.listeerpDemandeur LIKE :listeerpDemandeur')
                        ->setParameter('listeerpDemandeur', '%' . $_GET['demandeur'] . '%' );
                }

                if(isset($_GET['nom_erp'])){
                  $_SESSION['request']['nom_erp'] = $_GET['nom_erp'];
                    $queryBuilder
                        ->andWhere('dba.listeErpNomErp LIKE :listeErpNomErp')
                        ->setParameter('listeErpNomErp', '%' . $_GET['nom_erp'] . '%' );
                }

                if(isset($_GET['nom_voie'])){
                  $_SESSION['request']['nom_voie'] = $_GET['nom_voie'];
                    $queryBuilder
                        ->andWhere('dba.listeerpNomVoie LIKE :listeerpNomVoie')
                        ->setParameter('listeerpNomVoie', '%' . $_GET['nom_voie'] . '%' );
                }

                if(isset($_GET['siret'])){
                  $_SESSION['request']['siret'] = $_GET['siret'];
                    $queryBuilder
                        ->andWhere('dba.listeerpSiret LIKE :listeerpSiret')
                        ->setParameter('listeerpSiret', '%' . $_GET['siret'] . '%' );
                }

                $dbaListeerps = $queryBuilder->getQuery();
                if (empty($_SESSION['request'])) {
                  $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
                }
                $paginator = $this->get('knp_paginator');

                $result =$paginator->paginate(
                    $dbaListeerps,
                    $request->query->getInt('page', 1),
                    $request->query->getInt('limit', 10)
                );
                $choices = [5, 10, 15, 20, 25, 30];
                return $this->render('dbalisteerp/index.html.twig', array(
                    'dbaListeerps' => $result,
                    'choices' => $choices,
                ));

        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * @Route("/liste/index/csv", name="export")
     */
    public function exportAction(Request $request){

      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
            if(!empty($_SESSION['request'])) {
              if (array_key_exists('departement', $_SESSION['request']) == true) {
                $queryBuilder->andWhere('dba.listeerpDepartement LIKE :departement')
                ->setParameter('departement', $_SESSION['request']['departement']);
              }
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

                $rows[] = implode(';', $data);
            }

            $content = implode("\n", $rows);
            $response = new Response($content);
            $response->headers->set('Content-Type', 'text/csv');

            return $this->render('dbalisteerp/csv.html.twig', array(
                'dbaListeerps' => $result,
            ), $response);

      } else {

        return $this->redirectToRoute('fos_user_security_login');
      }
    }

    public function newAction() {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          $errors = [];
          $em = $this->getDoctrine()->getManager();
          $types = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
          $categories = $em->getRepository('cpossibleBundle:DbaCategorie')->findAll();
          return $this->render('dbalisteerp/new.html.twig', [
            'errors' => $errors,
            'types' => $types,
            'categories' => $categories,
          ]);
        } else {

        return $this->redirectToRoute('fos_user_security_login');
      }
    }
    /**
     * Creates a new dbaListeerp entity.
     *
     */
    public function insertAction(Request $request)
    {

        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $erp = new Dbalisteerp();
                // $response can hold errors which are at index 1
                $response = $this->insertion($request, $erp);
                if ($response[1]) {
                  dump($response);die;
                  $erp = $this->erpIfErrors($response[0], $response[2]);
                  $action = '/liste/insert';
                  return $this->rendering($response[0], $response[1], $action);
                }
                return $this->redirectToRoute('dbalisteerp_new');

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    // /**
    //  * Finds and displays a dbaListeerp entity.
    //  *
    //  */
    // public function showAction(DbaListeerp $dbaListeerp)
    // {

    //     $deleteForm = $this->createDeleteForm($dbaListeerp);
    //     return $this->render('dbalisteerp/show.html.twig', array(
    //         'dbaListeerp' => $dbaListeerp,
    //         'delete_form' => $deleteForm->createView(),
    //     ));
    // }

    /**
     * 
     *
     */
    public function editAction(/*Request $request,*/DbaListeerp $erp)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

              $errors = [];
              $action = '/'.$erp->getListeerpid().'/update';
              return $this->rendering($erp, $errors, $action);

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function editOneAction(Request $request, DbaListeerp $erp) {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          $em = $this->getDoctrine()->getManager();
          $response = $this->insertion($request, $erp);
          // dump($response);die;
          if ($response[1]) {
            $erp = $this->erpIfErrors($response[0], $response[2]);
            $action = '/'.$erp->getListeerpid().'/update';
            return $this->rendering($erp, $response[1], $action);
          }
          return $this->redirect('/liste/index');
        } else {
            return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');
        }
    }

    /**
     * Deletes a dbaListeerp entity.
     *
     */
    public function deleteAction(DbaListeerp $erp)
    { 
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          $em = $this->getDoctrine()->getManager();
          $em->remove($erp);
          $em->flush();
          return $this->redirectToRoute('dbalisteerp_index');
      } else {
          return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');
      }
    }


    /**
    * @Route("/last/{number}", name="dbalisteerp_last", requirements={"number"="\d+"})
    */
    public function lastAction(Request $request, $number) {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          // dump($number);die;
          $choices = [5, 10, 15, 20, 25, 30];
          if (!in_array($number, $choices)) {
            return $this->redirectToRoute('dbalisteerp_index');
          }
          $em = $this->getDoctrine()->getManager();
          $erps = array_slice($em->getRepository('cpossibleBundle:DbaListeerp')->findAll(), -$number);

          $paginator = $this->get('knp_paginator');
          $result =$paginator->paginate(
              $erps,
              $request->query->getInt('page', 1),
              $request->query->getInt('limit', 10)
          );
          return $this->render('dbalisteerp/index.html.twig', [
            'dbaListeerps' => $result,
            'choices' => $choices
          ]);
        }
        else {
          return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');
        }
    }

    // /**
    //  * Creates a form to delete a dbaListeerp entity.
    //  *
    //  * @param DbaListeerp $dbaListeerp The dbaListeerp entity
    //  *
    //  * @return \Symfony\Component\Form\Form The form
    //  */
    // private function createDeleteForm(DbaListeerp $dbaListeerp)
    // {
    //     return $this->createFormBuilder()
    //         ->setAction($this->generateUrl('dbalisteerp_delete', array('listeerpId' => $dbaListeerp->getListeerpid())))
    //         ->setMethod('DELETE')
    //         ->getForm()
    //     ;
    // }

     /** function used in the form builder to get types as an array
    *
    */
    private function getTypes() {
      $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
        $arraytypes = [];
        foreach ($types as $key => $value) {
          $test = [];
          $test = [$value->getTypeactiviteNom() => $value->getTypeactiviteCode()];
          // dump($value->getTypeactiviteNom());
          array_push($arraytypes, $test);
        }
        return $arraytypes;
    }

    protected function getNormalizedAddress($address) {
        $em = $this->getDoctrine()->getManager();
        $tempAdress = $address; // Here like: "Place de l'Europe" (whithout whitespace at the end)
        $adressExploded = explode(" ",$tempAdress);
        $intitule_voie = $adressExploded[0]; // "Place"
        $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
        $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
        ->setParameter('intitulevoieNom', $intitule_voie);
        $result = $q->getQuery();
        // Here we want to get the nom de voie as we wish to put in ddb like "PL"
        $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
        $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
        $fulladdress = "";
        $fulladdress .= $voie;
        for ($i=1; $i < count($adressExploded) ; $i++) {
          $fulladdress .= " " .mb_strtoupper($adressExploded[$i], 'UTF-8');
        }
        $fulladdress = str_replace('DR', 'DOCTEUR', $fulladdress);
        return $fulladdress;
    }

    protected function insertion($request, $erp) {
      $em = $this->getDoctrine()->getManager();

      $erp->setListeerpNumeroVoie($request->get('numero_rue'));
      $erp->setListeerpDateValidAdap($request->get('date_valid'));
      $erp->setListeerpLatitude($request->get('lat'));
      $erp->setListeerpLongitude($request->get('lng'));
      $erp->setListeerpDelaiAdap($request->get('delai'));
      $erp->setListeerpNature($request->get('nature'));
      $erp->setListeerpTypedossier($request->get('dossier'));
      $erp->setListeerpCategorie($request->get('categorie'));
      $erp->setListeerpDemandeur($request->get('demandeur'));
      $erp->setListeErpNomErp($request->get('nom_erp'));
      $erp->setListeerpIdAdap($request->get('id_adap'));
      $erp->setListeerpStatut(0);
      $errors = [];

      $qb = $em->getRepository('cpossibleBundle:Commune')->createQueryBuilder('c')
             ->select('c.codePostal')
             ->getQuery()
             ->getArrayResult();
      // It returns me an array of array instead of an array of values so i tricked it but i thing there is another 
      // and better trick to do it
      $codePostaux = [];
      foreach ($qb as $cp) {
        array_push($codePostaux, strval($cp['codePostal']));
      }
      if (!in_array($request->get('code_postal'), $codePostaux)) {
        $errors['code_postal'] = "Le code postal renseigné ne correspond à aucun code postal connus";
      }

      $types = "";
      $i = 0;
      foreach ($request->get('types') as $type) {
          $types .= $type;
          $i++;
          if ($i !== count($request->get('types'))) {
              $types .= ',';
          }
      }
      $erp->setListeerpType($types);

      if ($request->get('rue')) {
        $fulladdress = $this->getNormalizedAddress($request->get('rue'));
        $erp->setListeerpNomVoie($fulladdress);
      }
      // -- CHECK COMMUNE --
      $communeGoogle = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nomGoogle' => $request->get('commune')]);
      $commune = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => mb_strtoupper($request->get('commune'), 'UTF-8')]);
      $cplyon = ['69001', '69002', '69003', '69004', '69005', '69006', '69007', '69008', '69009'];

      // Trying to set up an array that hold LYON 01, LYON 02, etc... to check the commune 
      $lyon = [];
      foreach ($cplyon as $cp) {
        $var = "LYON ".substr($cp, -2);
        array_push($lyon, $var);
      }
      if ($communeGoogle) {
        $erp->setListeerpNomCommune($communeGoogle->getNom());
        $erp->setListeerpCodePostal(strval($communeGoogle->getCodePostal()));
        $erp->setListeerpCodeInsee(strval($communeGoogle->getCodeInsee()));
      }
      else if (in_array(mb_strtoupper($request->get('commune'), 'UTF-8'), $lyon)) {
        $erp->setListeerpNomCommune(mb_strtoupper($request->get('commune'), 'UTF-8'));
        $commune = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => $erp->getListeerpNomCommune()]);
        $erp->setListeerpCodePostal(strval($commune->getCodePostal()));
        $erp->setListeerpCodeInsee(strval($commune->getCodeInsee()));
      }
      else if ($commune) {
        $erp->setListeerpNomCommune($commune->getNom());
        $erp->setListeerpCodePostal(strval($commune->getCodePostal()));
        $erp->setListeerpCodeInsee(strval($commune->getCodeInsee()));
      }
      // if the CP is correct and one of the ones lyon uses, and the input commune is like lyon
      // then =>
      else if (!array_key_exists('code_postal', $errors) && ((mb_strtoupper($request->get('commune'), 'UTF-8') == 'LYON') && (in_array($request->get('code_postal'), $cplyon)))) {
          $arrondissement = substr($request->get('code_postal'), -2);
          $erp->setListeerpNomCommune('LYON '.$arrondissement);
          $commune = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => $erp->getListeerpNomCommune()]);
          $erp->setListeerpCodePostal(strval($commune->getCodePostal()));
          $erp->setListeerpCodeInsee(strval($commune->getCodeInsee()));
      }
      else {
        $errors['commune'] = "La commune renseignée ne correspond à aucune commune connue";
        // dump($errors);die;
        $response = [$erp, $errors, $request->request];
        return $response;
      }
      // -- CHECK DEPARTEMENT --
      $dpt = $em->getRepository('cpossibleBundle:DbaDepartement')->findOneBy(['departementNom' => $request->get('departement')]);
      if ($dpt) {
        $erp->setListeerpDepartement($dpt->getDepartementCode());
      }
      else {
        $errors['département'] = "Le département renseigné ne correspond à aucun département connus";
        $response = [$erp, $errors, $request->request];
        return $response;
      }

      // -- CHECK SIRET --

      if (($request->get('siret') == "") || !ctype_digit($request->get('siret'))) {
        $erp->setListeerpSiret('0');
      }
      else {
        $erp->setListeerpSiret($request->get('siret'));
      }
      
      $em->persist($erp);
      $em->flush();
      $response = [$erp, $errors, $request->request];
      return $response;
    }

    protected function rendering($erp, $errors, $action) {
      $em = $this->getDoctrine()->getManager();
      $types = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
      $categories = $em->getRepository('cpossibleBundle:DbaCategorie')->findAll();
      $dossiers = $em->getRepository('cpossibleBundle:DbaTypedossier')->findAll();
      $natures = $em->getRepository('cpossibleBundle:DbaTypeerp')->findAll();
      // $errors = [];

      $erptypes = $erp->getListeerpType();
      $newtypes = str_replace(["/", "-", ","], [" ", " ", " "], $erptypes);
      $erptypes = explode(" ", $newtypes);
      // /{{erp.listeerpId}}/update
      // $action = '/'.$erp->getListeerpid().'/update';
      return $this->render('dbalisteerp/edit.html.twig', [
        'action' => $action,
        'erp' => $erp,
        'erptypes' => $erptypes,
        'errors' => $errors,
        'types' => $types,
        'categories' => $categories,
        'natures' => $natures,
        'dossiers' => $dossiers,
      ]);
    }
    /**
    * function that take the request from a new erp or an edited one 
    * and setup the erp with those data even if they are bad so i can display them as
    * they were entered by user
    */
    protected function erpIfErrors($erp, $request) {
      // dump($request);die;
      $erp->setListeerpSiret($request->get('siret'));
      $erp->setListeerpNumeroVoie($request->get('numero_rue'));
      $erp->setListeerpNomVoie($request->get('rue'));
      $erp->setListeerpDepartement($request->get('departement'));
      $erp->setListeerpNomCommune($request->get('commune'));
      $erp->setListeerpCodePostal($request->get('code_postal'));
      $erp->setListeerpNumeroVoie($request->get('numero_rue'));
      $erp->setListeerpDateValidAdap($request->get('date_valid'));
      $erp->setListeerpLatitude($request->get('lat'));
      $erp->setListeerpLongitude($request->get('lng'));
      $erp->setListeerpDelaiAdap($request->get('delai'));
      $erp->setListeerpNature($request->get('nature'));
      $erp->setListeerpTypedossier($request->get('dossier'));
      $erp->setListeerpCategorie($request->get('categorie'));
      $erp->setListeerpDemandeur($request->get('demandeur'));
      $erp->setListeErpNomErp($request->get('nom_erp'));
      $erp->setListeerpIdAdap($request->get('id_adap'));
      $erp->setListeerpStatut(0);
      return $erp;
    }
}
