<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dbaListeerps = $em->getRepository('cpossibleBundle:DbaListeerp')->findAll();

        return $this->render('cpossibleBundle:Home:accueil.html.twig', array(
            'dbaListeerps' => $dbaListeerps,
        ));

        }
}
