<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/edituser")
     */
    public function editAction()
    {
        return $this->render('cpossibleBundle:User:edit.html.twig', array(
            // ...
        ));
    }

}
