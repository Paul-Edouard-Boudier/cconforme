<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use cpossibleBundle\Entity\DbaListeerp;

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
      $test = 'Ceci représente un test de niveau 1';
      return $this->render('cpossibleBundle:Report:new.html.twig', [
        'test' => $test,
      ]);
    }

    public function insertOneAction(Request $request) {
      // dump($request->request);die;
      $em = $this->getDoctrine()->getManager();

      $report = new Report();
      $report->setAdresseErp($request->request->get('adresse_erp'));
      $report->setAdresseSignalee($request->request->get('adresse_signalee'));
      $report->setUserEmail($request->request->get('email'));
      $report->setMessage($request->request->get('message'));
      // dump($report);die;
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
