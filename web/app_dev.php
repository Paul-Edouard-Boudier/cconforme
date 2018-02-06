<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

// Here is what you need to add for users from where i was working, on branch Users

// vendor/friendofsymfony/user-bundle/ressources/config/doctrine-mapping/user.orm.xml:

        // <field name="departement" column="departement" type="string" length="45" nullable="true"/>

        // <field name="commune" column="commune" type="string" length="255" nullable="true"/>

        // <field name="epci_metropole" column="epci_metropole" type="string" length="255" nullable="true"/>

// vendor/friendofsymfony/user-bundle/Model/User.php: + getter and setter 
//      /**
//      *
//      * @var string
//      */
//     protected $departement;
//  *
//      *
//      * @var string
     
//     protected $commune;

// /**
//      *
//      * @var string
//      */
//     protected $epci_metropole;
// And I think that's it




if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['192.168.33.40', '::1'], true) || PHP_SAPI === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
