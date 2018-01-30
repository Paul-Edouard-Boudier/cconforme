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

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {

            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {


                $em = $this->getDoctrine()->getManager();

                $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
                $_SESSION['request'] = [];
                if(isset($_GET['adap'])){
                  $_SESSION['request']['adap'] = $_GET['adap'];
                    $queryBuilder
                        ->andWhere('dba.listeerpIdAdap LIKE :listeerpIdAdap')
                        ->setParameter('listeerpIdAdap', '%' . $_GET['adap'] . '%' );
                }

                if(isset($_GET['commune'])){
                  // $_SESSION['request']['commune'] = $request->query->getAlnum('commune');
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
                // $test = $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(['listeerpId' => 69031198]);
                // dump($test);die;
                $choices = [5, 10, 15, 20, 25, 30];
                return $this->render('dbalisteerp/index.html.twig', array(
                    'dbaListeerps' => $result,
                    'choices' => $choices,
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

                $rows[] = implode(';', $data);
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

    public function newAction() {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
          $em = $this->getDoctrine()->getManager();
          $types = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
          $categories = $em->getRepository('cpossibleBundle:DbaCategorie')->findAll();
          return $this->render('dbalisteerp/new.html.twig', [
            'types' => $types,
            'categories' => $categories,
          ]);
        }
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
            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
                $em = $this->getDoctrine()->getManager();
                $erp = new Dbalisteerp();

                $dpt = $em->getRepository('cpossibleBundle:DbaDepartement')->findOneBy(['departementNom' => $request->get('departement')])->getDepartementCode();
                $fulladdress = $this->getNormalizedAddress($request->get('rue'));

                $types = "";
                $i = 0;
                foreach ($request->get('types') as $type) {
                    $types .= $type;
                    $i++;
                    if ($i !== count($request->get('types'))) {
                        $types .= ',';
                    }
                }

                $postalCodes = ['69001', '69002', '69003', '69004', '69005', '69006', '69007', '69008', '69009'];
                if (in_array($request->get('code_postal'), $postalCodes)) {
                    $insee = strval($em->getRepository('cpossibleBundle:Commune')->findOneBy(['codePostal' => $request->get('code_postal')])->getCodeInsee());
                }
                else {
                    $tempCommune = preg_replace('/\-+|\'+/', ' ', strtoupper($request->get('commune')));
                    if (strpos($tempCommune, 'SAINT') !== false) {
                        $tempCommune = str_replace('SAINT', 'ST', $tempCommune);
                    }
                    elseif (strpos($tempCommune, 'SAINTE') !== false) {
                        $tempCommune = str_replace('SAINTE', 'STE', $tempCommune);
                    }
                    $insee = strval($em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => $tempCommune])->getCodeInsee());
                }
                
                $erp->setListeerpType($types);
                $erp->setListeerpNomVoie($fulladdress);
                $erp->setListeerpDepartement($dpt);
                $erp->setListeerpCodeInsee($insee);
                $erp->setListeerpNumeroVoie($request->get('numero_rue'));
                $erp->setListeerpCodePostal($request->get('code_postal'));
                $erp->setListeerpNomCommune($request->get('commune'));
                $erp->setListeerpDateValidAdap($request->get('date_valid'));
                $erp->setListeerpLatitude($request->get('lat'));
                $erp->setListeerpLongitude($request->get('lng'));
                $erp->setListeerpDelaiAdap($request->get('delai'));
                $erp->setListeerpNature($request->get('nature'));
                $erp->setListeerpTypedossier($request->get('dossier'));
                $erp->setListeerpCategorie($request->get('categorie'));
                $erp->setListeerpDemandeur($request->get('demandeur'));
                $erp->setListeErpNomErp($request->get('nom_erp'));
                $erp->setListeerpSiret($request->get('siret'));
                $erp->setListeerpIdAdap($request->get('id_adap'));
                $erp->setListeerpStatut(0);
                // dump($erp);die;
                $em->persist($erp);
                $em->flush();
                return $this->redirectToRoute('dbalisteerp_new');

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
    * @Route("/last/{number}", name="dbalisteerp_last", requirements={"number"="\d+"})
    */
    public function lastAction(Request $request, $number) {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
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
      else {
        return $this->redirectToRoute('fos_user_security_login');
      }
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
        $tempAdress = $address; // Here: "Place de l'Europe" (whithout whitespcae at the end)
        $adressExploded = explode(" ",$tempAdress);
        $intitule_voie = $adressExploded[0]; // "Place"
        $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
        $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
        ->setParameter('intitulevoieNom', '%' . $intitule_voie . '%' );
        $result = $q->getQuery();
        // Here we want to get the nom de voie as we wish to put in ddb like "PL"
        $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
        $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
        $fulladdress = "";
        $fulladdress .= $voie;
        for ($i=1; $i < count($adressExploded) ; $i++) {
          $fulladdress .= " " .strtoupper($adressExploded[$i]);
        }
        return $fulladdress;
    }
}
