<?php

namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReportController extends Controller
{
    public function indexAction() {

      return $this->render('cpossibleBundle:Report:index.html.twig', array(
          // ...
      ));
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

}
