<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use cpossibleBundle\Entity\DbaListeerp;
use \Datetime;
use \DateInterval;

class ReportController extends Controller
{
    public function indexAction() {
      $securityContext = $this->container->get('security.authorization_checker');

      if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
          if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
            $em = $this->getDoctrine()->getManager();
            $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
            // if (empty($reports)) {
            //   $reports = 'empty reports';
            // }
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
      $report->setAdresseErp($request->request->get('adresse_erp'));
      $report->setAdresseSignalee($request->request->get('adresse_signalee'));
      $report->setUserEmail($request->request->get('email'));
      $report->setMessage($fullMessage);
      $em->persist($report);
      $em->flush();

      return $this->render('cpossibleBundle:Home:accueil.html.twig');
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
