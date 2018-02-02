<?php

namespace cpossibleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Unirest;

class SiretController extends Controller {

	public function checkSiretAction(Request $request) {
		$token = "owliIf6GFqVoA6qASY8NXnoPW3zCliOl";
		// get siret from request
		// $siret = "80136322700027";
		$siret = $request->get('siret');
		// $tokengoogle = 
		$response = Unirest\Request::get('https://entreprise.api.gouv.fr/v2/etablissements/'.$siret.'?token='.$token.'');
		if (!isset($response->body->errors)) {
			$data['enseigne'] = $response->body->etablissement->enseigne;
			$data['demandeur'] = $response->body->etablissement->adresse->l1;
			$fullAdressForSearch = $response->body->etablissement->adresse->l4." ".$response->body->etablissement->adresse->l6;

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
      // dump($data);die;
			// dump($result);die;
			return new JsonResponse($data);
		}
		else {
			$data = 0;
			return new JsonResponse($data);
		}
	}

}
