<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Unirest;

class CheckingController extends Controller {

	public function siretAction(Request $request) {
		$securityContext = $this->container->get('security.authorization_checker');
		if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
			if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
				// token given by my tutor;
				$token = "owliIf6GFqVoA6qASY8NXnoPW3zCliOl";
				// get siret from request
				$siret = $request->get('siret');
				// $tokengoogle = 
				$response = Unirest\Request::get('https://entreprise.api.gouv.fr/v2/etablissements/'.$siret.'?token='.$token.'');
				if (!isset($response->body->errors)) {
					$data['enseigne'] = $response->body->etablissement->enseigne;
					$data['demandeur'] = $response->body->etablissement->adresse->l1;
					$fullAdressForSearch = $response->body->etablissement->adresse->l4." ".$response->body->etablissement->adresse->l6;
					// $key comes from base.html.twig
					$key = "AIzaSyBapkuSxVaHJ0CZhOBk3H4NnHARd4H_btk";
					$response = Unirest\Request::get('https://maps.googleapis.com/maps/api/geocode/json?address='.$fullAdressForSearch.'&key='.$key.'');
					$result = $response->body->results[0];
					$components = ['street_number', 'route', 'locality', 'administrative_area_level_2', 'postal_code'];
					$address_components = $result->address_components;
					foreach ($address_components as $address) {
						$type = $address->types[0];
						if (in_array($type, $components)) {
							$data['address'][$type] = $address->short_name;
						}
					}
					$data['lat'] = strval($result->geometry->location->lat);
					$data['lng'] = strval($result->geometry->location->lng);
					return new JsonResponse($data);
				}
				else {
					$data = 0;
					return new JsonResponse($data);
				}
			} else {
			  return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');
			}
		} else {
		return $this->redirectToRoute('fos_user_security_login');
		}
	}

	public function normalizeDadabaseAction() {
		$securityContext = $this->container->get('security.authorization_checker');
		if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
			if ($this->getUser() && $this->getUser()->getusername() == 'adminresic') {
				$em = $this->getDoctrine()->getManager();
				$q = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('erp');
				$q->andWhere('erp.listeerpAdresseTemporaire is not null');
				$erps = $q->getQuery()->getResult();
				$i = 0;
				foreach ($erps as $erp) {
					$fullAdressForSearch = $erp->getListeerpAdresseTemporaire();
	        $key = "AIzaSyBapkuSxVaHJ0CZhOBk3H4NnHARd4H_btk";
					$response = Unirest\Request::get('https://maps.googleapis.com/maps/api/geocode/json?address='.$fullAdressForSearch.'&key='.$key.'');
					if ($response->body->results[0]) {
						// $data will holds data from google request use to update erp
						$result = $response->body->results[0];
						$components = ['street_number', 'route', 'locality', 'administrative_area_level_2', 'postal_code'];
						$address_components = $result->address_components;
						foreach ($address_components as $address) {
							$type = $address->types[0];
							if (in_array($type, $components)) {
								$data['address'][$type] = $address->short_name;
							}
						}
						$data['lat'] = strval($result->geometry->location->lat);
						$data['lng'] = strval($result->geometry->location->lng);
						$address = $this->getNormalizedAddress($data['address']['route']);

						$q = $em->getRepository('cpossibleBundle:DbaDepartement')->createQueryBuilder('dpt');
						$q->andWhere('dpt.departementNom LIKE :nom')
						->setParameter('nom', $data['address']['administrative_area_level_2']);
						$result = $q->getQuery()->getResult()[0];
						$departement = $result->getDepartementCode();
						$data['address']['administrative_area_level_2'] = $departement;
						$data['address']['route'] = $address;
						if (strtoupper($data['address']['locality']) == 'LYON') {
							$arrondissement = substr($data['address']['postal_code'], -2);
							$data['address']['locality'] = 'LYON '.$arrondissement;
						}
						$communeGoogle = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nomGoogle' => $data['address']['locality']]);
						if ($communeGoogle) {
							$data['address']['locality'] = $communeGoogle->getNom();
							$data['address']['insee'] = strval($communeGoogle->getCodeInsee());
						} else {
							$commune = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => $data['address']['locality']]);
							$data['address']['locality'] = $commune->getNom();
							$data['address']['insee'] = strval($commune->getCodeInsee());
						}
					}
					$erp->setListeerpLongitude($data['lng']);
					$erp->setListeerpLatitude($data['lat']);
					$erp->setListeerpNomCommune($data['address']['locality']);
					$erp->setListeerpNumeroVoie($data['address']['street_number']);
					$erp->setListeerpNomVoie($data['address']['route']);
					$erp->setListeerpDepartement($data['address']['administrative_area_level_2']);
					$erp->setListeerpCodePostal($data['address']['postal_code']);
					$erp->setListeerpCodeInsee($data['address']['insee']);
					$erp->setListeerpAdresseTemporaire(null);
					$em->persist($erp);
					$em->flush();
					$i ++;
				}
    		$label = 'établissement(s) mis à jour.';
    		$error = [];
    		return $this->render('cpossibleBundle:TPS:new.html.twig', ['error' => $error, 'count' => $i, 'label' => $label]);
			} else {
			  return $this->redirectToRoute('cpossibleBundle:Home:accueil.html.twig');
			}
		} else {
		return $this->redirectToRoute('fos_user_security_login');
		}
	}

	protected function getNormalizedAddress($address) {
    $em = $this->getDoctrine()->getManager();
    $tempAdress = $address; // Here like: "Place de l'Europe" (whithout whitespcae at the end)
    $adressExploded = explode(" ",$tempAdress);
    $intitule_voie = $adressExploded[0]; // "Place"
    $intitule_voie = str_replace('Ave.', 'AVENUE', $intitule_voie);
    $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
    $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
    ->setParameter('intitulevoieNom', $intitule_voie);
    $result = $q->getQuery();
    // Here we want to get the nom de voie as we wish to put in ddb like "PL"
    $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
    $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
    $fulladdress = "";
    $fulladdress .= $voie;
    for ($i=1; $i < count($adressExploded) ; $i++) {
      $fulladdress .= " " .mb_strtoupper($adressExploded[$i], 'UTF-8');
    }
    $fulladdress = str_replace('DR', 'DOCTEUR', $fulladdress);
    return $fulladdress;
  }
}
