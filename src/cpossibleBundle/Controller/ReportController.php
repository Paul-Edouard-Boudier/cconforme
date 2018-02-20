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
        // -----------------
        // Actual code for the controller
        $em = $this->getDoctrine()->getManager();
        $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
        return $this->render('cpossibleBundle:Report:index.html.twig', [
          'reports' => $reports,
        ]);
      } else {
          return $this->redirectToRoute('fos_user_security_login');
      }
    }

    public function formAction() {
      // $securityContext = $this->container->get('security.authorization_checker');

      // if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
        return $this->render('cpossibleBundle:Report:new.html.twig');
      // } else {
      //     return $this->redirectToRoute('fos_user_security_login');
      // }
    }

    public function insertOneAction(Request $request) {
      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
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
      } else {
          return $this->redirectToRoute('fos_user_security_login');
      }
    }

    public function deleteOneAction($id) {
      $securityContext = $this->container->get('security.authorization_checker');
      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
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
    }
}
