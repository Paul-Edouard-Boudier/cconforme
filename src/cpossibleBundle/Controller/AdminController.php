<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function adminPannelAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {

            if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
                return $this->render('cpossibleBundle:Admin:admin_pannel.html.twig');

            }

        } else {

            return $this->redirectToRoute('fos_user_security_login');
        }
    }
}
