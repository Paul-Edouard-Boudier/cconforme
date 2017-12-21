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
      $em = $this->getDoctrine()->getManager();
      $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
      return $this->render('cpossibleBundle:Report:index.html.twig', [
        'reports' => $reports,
      ]);
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

      $em = $this->getDoctrine()->getManager();
      $reports = $em->getRepository('cpossibleBundle:Report')->findAll();
      return $this->render('cpossibleBundle:Report:index.html.twig', [
        'reports' => $reports,
      ]);
    }

    public function testAction()
    {
      $em = $this->getDoctrine()->getManager();
      $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();
      return $this->render('dbalisteerp/index.html.twig', array(
          'dbaListeerps' => $dbaListeerps,
      ));
    }

}
