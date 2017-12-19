<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportController extends Controller
{
    public function indexAction()
    {
        return $this->render('cpossibleBundle:Report:index.html.twig', array(
            // ...
        ));
    }

}
