<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('cpossibleBundle:Home:accueil.html.twig');
    }
}
