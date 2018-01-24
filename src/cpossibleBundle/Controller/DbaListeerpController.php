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
                dump($request->request);
                // $dbaListeerp = new Dbalisteerp();
                // // $types = $this->getTypes();
                // $form = $this->createForm('cpossibleBundle\Form\DbaListeerpType', $dbaListeerp);
                // $form->handleRequest($request);
                // if ($form->isSubmitted() && $form->isValid()) {
                //   $types = "";
                //   foreach ($dbaListeerp->getListeerpType() as $key => $type) {
                //     $types .= $type.",";
                //   }
                //   $dbaListeerp->setListeerpType($types);
                //   $em->persist($dbaListeerp);
                //   $em->flush();
                //   return $this->redirectToRoute('dbalisteerp_show', array('listeerpId' => $dbaListeerp->getListeerpid()));
                // }

                // return $this->render('dbalisteerp/new.html.twig', array(
                //     'dbaListeerp' => $dbaListeerp,
                //     'form' => $form->createView(),
                //     // 'types' =>$types,
                // ));

              // -------------------------
                $erp = new Dbalisteerp();

                $dpt = $em->getRepository('cpossibleBundle:DbaDepartement')->findOneBy(['departementNom' => $request->request->get('departement')])->getDepartementCode();

                $tempAdress = $request->request->get('rue'); // Here: "Place de l'Europe" (whithout whitespcae at the end)
                $adressExploded = explode(" ",$tempAdress);
                $intitule_voie = $adressExploded[0]; // "Place"

                $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
                $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
                  ->setParameter('intitulevoieNom', '%' . $intitule_voie . '%' );
                $result = $q->getQuery();
                // Here we want to get the nom de voie as we wish to put in ddb like "PL"
                $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
                $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
                $fulladress = "";
                $fulladress .= $voie;
                for ($i=1; $i < count($adressExploded) ; $i++) {
                  $fulladress .= " " .strtoupper($adressExploded[$i]);
                }

                $types = "";
                foreach ($request->get('types') as $type) {
                  $types .= $type.",";
                }
                
                $erp->setListeerpType($types);
                $erp->setListeerpNomVoie($fulladress);
                $erp->setListeerpDepartement($dpt);
                $erp->setListeerpNumeroVoie($request->request->get('numero_rue'));
                $erp->setListeerpCodePostal($request->request->get('code_postal'));
                $erp->setListeerpNomCommune($request->request->get('commune'));
                $erp->setListeerpDateValidAdap($request->get('date_valid'));
                $erp->setListeerpLatitude($request->request->get('lat'));
                $erp->setListeerpLongitude($request->request->get('lng'));
                $erp->setListeerpDelaiAdap($request->get('delai'));
                $erp->setListeerpNature($request->get('nature'));
                $erp->setListeerpTypedossier($request->get('dossier'));
                $erp->setListeerpCategorie($request->request->get('categorie'));
                $erp->setListeerpDemandeur($request->request->get('demandeur'));
                $erp->setListeErpNomErp($request->request->get('nom_erp'));
                $erp->setListeerpSiret($request->request->get('siret'));
                $erp->setListeerpIdAdap($request->request->get('id_adap'));
                $erp->setListeerpIdIgn($request->request->get('id_ign'));
                $erp->setListeerpStatut(0);
                // $em->persist($erp);
                // $em->flush();
                dump($erp);die;

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
}
