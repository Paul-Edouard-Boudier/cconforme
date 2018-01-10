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

        // instantiation, when using it as a component

        // $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        // // dump($serializer);die;
        // // instantiation, when using it inside the Symfony framework
        // // $serializer = $container->get('serializer');
        // $em = $this->getDoctrine()->getManager();
        //
        // $queryBuilder = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('dba');
        //
        // if($request->query->getAlnum('adap')){
        //     $queryBuilder
        //         ->where('dba.listeerpIdAdap LIKE :listeerpIdAdap')
        //         ->setParameter('listeerpIdAdap', '%' . $request->query->getAlnum('adap') . '%' );
        // }
        //
        // if($request->query->getAlnum('commune')){
        //     $queryBuilder
        //         ->where('dba.listeerpNomCommune LIKE :listeerpNomCommune')
        //         ->setParameter('listeerpNomCommune', '%' . $request->query->getAlnum('commune') . '%' );
        // }
        //
        // if($request->query->getAlnum('demandeur')){
        //     $queryBuilder
        //         ->where('dba.listeerpDemandeur LIKE :listeerpDemandeur')
        //         ->setParameter('listeerpDemandeur', '%' . $request->query->getAlnum('demandeur') . '%' );
        // }
        //
        // if($request->query->getAlnum('nom_erp')){
        //     $queryBuilder
        //         ->where('dba.listeErpNomErp LIKE :listeErpNomErp')
        //         ->setParameter('listeErpNomErp', '%' . $request->query->getAlnum('nom_erp') . '%' );
        // }
        //
        // if($request->query->getAlnum('nom_voie')){
        //     $queryBuilder
        //         ->where('dba.listeerpNomVoie LIKE :listeerpNomVoie')
        //         ->setParameter('listeerpNomVoie', '%' . $request->query->getAlnum('nom_voie') . '%' );
        // }
        //
        // $data = strval($queryBuilder->getQuery());
        //
        // // encoding contents in CSV format
        // $serializer->encode($data, 'csv');

        // decoding CSV contents
        // $data = $serializer->decode(file_get_contents('data.csv'), 'csv');
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

    public function tpsAction() {
      //$procedure = $response->body->procedure;
      // On peut très bien faire une requete de procédure dynamique (donc en checkant le dpt par ex)
      // Et faire une requete de dossier pour chaque dossier trouvé
      $procedure = '1998';
      $token = '85cc86ebbca4d1b518db1f597256b365df4465de';

      // On récupère tous les dossiers d'une procédure (donc tous les dossier de cahque batiments)
      // Puis on fait une deuxième requete pour récupérer les infos pour chaque dossier trouvé
      // $response = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers?token='.$token.'');
      // $dossiers =$response->body->dossiers;
      // foreach ($dossiers as $dossier) {
      //   $subResponse = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers/'.$dossier->id.'?token='.$token.'');
      //   $entity = $subResponse->body->dossier;
        // On joue ici avec chaque entité afin de l'ajouter à la ddb
        // dump($entity->champs);
        //$type = $entity->champs[2]->value;
        echo '<pre>';
        $types = '["N : Restaurant et débit de boisson", "O : Hôtel, pension de famille, résidence de tourisme"]';
        var_dump($types);die;
        //$typesArray = explode(" ", $types);
        //var_dump($typesArray);die;
        // $em = $this->getDoctrine()->getManager();
        // $erp = new Dbalisteerp();
        // $erp->setListeerpDemandeur($entity->champs[0]->value);
        // $erp->setListeErpNomErp($entity->champs[11]->value);
        // $erp->setListeerpNature($entity->champs[15]->value);
        // // $em->persist($erp);
        // // $em->flush();
        // dump($erp);
      //}
      //dump('fin');die;
      return $this->render('cpossibleBundle:TPS:index.html.twig', ['procedure' => $procedure]);
    }

}
