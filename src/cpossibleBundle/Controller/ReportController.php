<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use cpossibleBundle\Entity\DbaListeerp;
use \Datetime;
use \DateInterval;
use Unirest;

class ReportController extends Controller
{
    public function indexAction() {
      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
            // -----------------
            // Actual code for the controller
            $em = $this->getDoctrine()->getManager();
            $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
            return $this->render('cpossibleBundle:Report:index.html.twig', [
              'reports' => $reports,
            ]);
            // ----------------------

            // code to change coordinates from actual base to google api

            // previous lng : 4.826645389
            // lng : 4.8266948
            // previous lat : 45.761189295
            // lat : 45.7613227
            // /!\ TEST CHANGE LAT AND LNG FOR EVERY ENTRIES OF DDB /!\
            // $erps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
            // // $status = 0;
            // foreach ($erps as $erp) {
            //     if ($erp->getListeerpNumeroVoie() != '') {
            //       $status = $erp->getListeerpId();
            //       echo($status);
            //         $address = $erp->getListeerpNumeroVoie() . ' ' . $erp->getListeerpNomVoie() . ' ' .$erp->getListeerpCodePostal();
            //         $key = "AIzaSyBapkuSxVaHJ0CZhOBk3H4NnHARd4H_btk";
            //         $response = Unirest\Request::get('https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key.'');
            //         if ($response->body->results) {
            //             $result = $response->body->results[0];
            //             $location = $result->geometry->location;
            //             // dump($erp->getListeerpLongitude());
            //             // dump($erp->getListeerpLatitude());
            //             $erp->setListeerpLongitude($location->lng);
            //             $erp->setListeerpLatitude($location->lat);
            //             // dump($erp->getListeerpLongitude());
            //             // dump($erp->getListeerpLatitude());
            //             // dump($location);die;
            //             // dump($erp);die;
            //             $em->persist($erp);
            //             $em->flush();
            //         }
            //     }
            // }
            // dump('ok well, did it worked ?');
            // /!\ END /!\

          } else {
              return $this->redirectToRoute('fos_user_security_login');
          }
      } else {
          return $this->redirectToRoute('fos_user_security_login');
      }
    }

    public function formAction() {
      return $this->render('cpossibleBundle:Report:new.html.twig', [
      ]);
    }

    public function insertOneAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $fullMessage = "";
      foreach ($request->request->get('message') as $message) {
        $fullMessage .= $message . " / ";
      }
      $report = new Report();
      $report->setNomErp($request->request->get('nom_erp'));
      $report->setAdresseSignalee($request->request->get('adresse_signalee'));
      $report->setUserEmail($request->request->get('email'));
      $report->setMessage($fullMessage);
      // dump($report);die;
      $em->persist($report);
      $em->flush();

      $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
      $typesErp = $em->getRepository('cpossibleBundle:DbaTypeactivite')->findAll();
      return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
          'dbaListeerps' => $dbaListeerps,
          'typesErp' => $typesErp,
      ));
    }

    public function deleteOneAction($id) {
      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
              $em = $this->getDoctrine()->getManager();
              $report = $em->getRepository('cpossibleBundle:Report')->find($id);
              if ($report != null) {
                $em->remove($report);
                $em->flush();
              }
              $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
              return $this->render('cpossibleBundle:Report:index.html.twig', [
                'reports' => $reports,
              ]);
          } else {
              return $this->redirectToRoute('fos_user_security_login');
          }
      } else {
          return $this->redirectToRoute('fos_user_security_login');
      }
    }
}
