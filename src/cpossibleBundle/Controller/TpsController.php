<?php
namespace cpossibleBundle\Controller;

use cpossibleBundle\Entity\DbaListeerp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Unirest;

class TpsController extends Controller
{

  public function tpsAction() {
    $securityContext = $this->container->get('security.authorization_checker');
    if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
      $em = $this->getDoctrine()->getManager();
      $dpts = $em->getRepository('cpossibleBundle:DbaDepartement')->findAll();
      return $this->render('cpossibleBundle:TPS:index.html.twig', ['dpts' => $dpts]);
    } 
    else {
      return $this->redirectToRoute('fos_user_security_login');
    }
  }
  /**
   * Function that will retrieve infos from the tps procedure and all the dossiers it has
   * and then update the database if the element doesn't exist already
   */
  public function newAction(Request $request) {
    $securityContext = $this->container->get('security.authorization_checker');
    if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
      $em = $this->getDoctrine()->getManager();
      $errors = [];
      $departement = $request->request->get('departement');
      

      $dpt = $em->getRepository('cpossibleBundle:DbaDepartement')->findOneBy(['departementCode' => $departement]);
      $procedure = $dpt->getDepartementProcedure();
      $token = $dpt->getDepartementToken();

      if ($procedure == null) {
          $errors['procedure'] = "Le département que vous avez indiqué ne contient aucune procédure actuellement.";
      }
      elseif ($token == null) {
          $errors['token'] = "Le département renseigné n'a pas donné accès au token administratif.";
      }

      if (!empty($errors)) {
        $count = null;
        $error = array_values($errors)[0];
        $label = '';
        return $this->render('cpossibleBundle:TPS:new.html.twig', ['error' => $error, 'count' => $count, 'label' => $label]);
      }
      // /!\ END CHECKING /!\

      // On récupère tous les dossiers d'une procédure (donc tous les dossier de chaque bâtiment)
      // Puis on fait une deuxième requete pour récupérer les infos pour chaque dossier trouvé
      $response = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers?token='.$token.'');

      $dossiers = $response->body->dossiers;
      $erpsArray = []; // array to count how many was inserted.
      foreach ($dossiers as $dossier) {
        $subResponse = Unirest\Request::get('https://tps.apientreprise.fr/api/v1/procedures/'.$procedure.'/dossiers/'.$dossier->id.'?token='.$token.'');
        $entity = $subResponse->body->dossier;
        if($this->exist($dossier, $entity, $em) == false) {
          // i retrieve an array s i can count how many items i have pushed into ddb
          array_push($erpsArray, $this->insert($dossier, $entity, $em, $erpsArray));
        }
      }
      $count = count($erpsArray);
      $error = [];
      $label = 'établissement(s) ajouté(s) à la base de donées.';
      return $this->render('cpossibleBundle:TPS:new.html.twig', ['error' => $error, 'count' => $count, 'label' => $label]);
    } 
    else {
      return $this->redirectToRoute('fos_user_security_login');
    }
  }

  /**
   * Function that check if an entity coming from tps is already existing in ddb
   */
  private function exist($dossier, $entity, $em) {
    foreach ($entity->champs as $champ) {
      $value = $champ->value;
      if ($champ->type_de_champ->libelle == "Siret" && (!empty($champ->value))) {
        // find one by siret where siret = $champ->value;
        return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(['listeerpSiret' => $champ->value]) ? true : false;
      }  elseif ($champ->type_de_champ->libelle == 'Département') {
        preg_match("/[0-9]{1,3}/", $champ->value, $departement);
        // return true if found, flase if not
        // check insert for information about id
        // return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(['listeerpId' => intval($departement[0].str_repeat("0", 6 - strlen(strval($dossier->id))).strval($dossier->id))]) ? true : false;
        return $em->getRepository('cpossibleBundle:DbaListeerp')->findOneBy(['listeerpDossierTps' => intval($departement[0].str_repeat("0", 6 - strlen(strval($dossier->id))).strval($dossier->id))]) ? true : false;
      }
    }
  }

  private function insert($dossier, $entity, $em, $erps) {
    $erp = new Dbalisteerp();
    // Here's how we get the entries
    foreach ($entity->champs as $champ) {
      $libelle = $champ->type_de_champ->libelle;
      $value = $champ->value;

      if ($libelle == 'Département') {
        // Regex to get the first 1 to 3 digits that hold the dpt number
        // $selector = "/[0-9]{1,3}/";
        preg_match("/[0-9]{1,3}/", $value, $departement);
        $erp->setListeerpDepartement($departement[0]);
        // Set an id like this: dpt_number + 6 digits(dossier id)
        // take length of dossier id
        // $length_dossier_id = strlen(strval($dossier->id));
        if (strlen(strval($dossier->id)) < 6) {
          // Check length of dossier->id and add as many 0 to fill the 6 digits that we need for the $erp->id
          $stringId = $departement[0].str_repeat("0", 6 - strlen(strval($dossier->id))).strval($dossier->id);
          $erp->setListeerpDossierTps(intval($stringId));
        } else {
          $stringId = $departement[0].strval($dossier->id);
          $erp->setListeerpDossierTps(intval($stringId));
        }
      }
      if ($libelle == "Type d'établissement") {
        $erp->setListeerpNature(strtolower($value));
      }
      if ($libelle == "Catégorie") {
        // $selector = "/[0-9]{1,2}/";
        preg_match("/[0-9]{1,2}/", $value, $categorie);
        $erp->setListeerpCategorie($categorie[0]);
      }
      if ($libelle == "Date de mise en conformité") {
        // Not sure if it's the right date
        $erp->setListeerpDateValidAdap($value);
      }
      if ($libelle == "Durée de la dérogation en année") {
        if (!empty($value)) {
          $erp->setListeerpDelaiAdap($value);
        }
      }
      if ($libelle == "Numéro d'adap") {
        if (!empty($value)) {
          $adap = $value;
        }
      }
      if ($libelle == "Nom de l'établissement, enseigne") {
        $erp->setListeErpNomErp($value);
      }
      if ($libelle == "Siret") {
        if (empty($value)) {
          $erp->setListeerpSiret('0');
        }
        else {
          $erp->setListeerpSiret($value);
        }
      }
      if ($libelle == "Type de déclaration") {
        $declaration = strtolower($value);
      }
      if ($libelle == "Types d'activités") {
        // select all character of $value between A-Z followed by whitespace
        $selector = "/[A-Z]{1,3}\s/";
        preg_match_all($selector, $value, $typesUgly);
        // delete whitespace from string that the previous regex returns us
        $typesBeautified = preg_replace('/\s+/', '', $typesUgly[0]);
        $types = "";
        $last = count($typesBeautified);
        $i = 0;
        foreach ($typesBeautified as $type) {
          if ($i == $last - 1) {
            $types .= $type;
          }
          else {
            $types .= $type.',';
          }
          $i ++;
        }
        $erp->setListeerpType($types);
      }
      if ($libelle == "Adresse") {
        // var wrote with underscore are the one coming from google
        // I think that my double checking is useless, maybe it is better to just work with what google gives me

        // /!\ GOOGLE REQUEST /!\
        // google request to retrieve lat and long from $value
        // api key that is in base.html.twig
        $key = "AIzaSyBapkuSxVaHJ0CZhOBk3H4NnHARd4H_btk";
        $response = Unirest\Request::get('https://maps.googleapis.com/maps/api/geocode/json?address='.$value.'&key='.$key.'');
        // $result holds data that we cant use
        $result = $response->body->results[0];
        // $adress_components holds every entries for and address
        $adress_components = $result->address_components;
        foreach ($adress_components as $adress) {
          if (in_array('street_number', $adress->types)) {
            $street_number = $adress->long_name;
          }
          if (in_array('postal_code', $adress->types)) {
            $postal_code = $adress->long_name;
          }
          if (in_array('route', $adress->types)) {
            $route_name = $adress->long_name;
          }
          // If we want to retrieve locality via the adress that google found
          if (in_array('locality', $adress->types)) {
            $nom_commune = $adress->long_name;
          }
        }
        $location = $result->geometry->location; // object that contain lat and lng
        if (strtoupper($nom_commune) == 'LYON') {
          $arrondissement = substr($postal_code, -2);
          $nom_commune = 'LYON '.$arrondissement;
        }
        $communeGoogle = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nomGoogle' => $nom_commune]);
        if ($communeGoogle) {
          $nom_commune = $communeGoogle->getNom();
          $insee = strval($communeGoogle->getCodeInsee());
        } else {
          $commune = $em->getRepository('cpossibleBundle:Commune')->findOneBy(['nom' => $nom_commune]);
          $nom_commune = $commune->getNom();
          $insee = strval($commune->getCodeInsee());
        }
        // /!\ END GOOGLE REQUEST /!\

        // Regex that split a string like this:
        // 11 Place de l'Europe 69006 Lyon
        // into an array like this:
        // ["full_adress_here","11", "Place de l'Europe ", "69006", "Lyon"];
        $selector = "/(\d{1,4})\s?([a-zA-ZÀ-Ÿ',\s]*\s?)?\s?([0-9]{5})\s?([a-zA-ZÀ-Ÿ',\/]*\s*)/";
        preg_match($selector, $value, $splitedAdress);
        $numeroVoie = $splitedAdress[1];
        $codePostal = $splitedAdress[3];
        $commune = $splitedAdress[4];

        // // Adress that we need to write like "PL DE L'EUROPE"
        // $tempAdress = $splitedAdress[2]; // Here: "Place de l'Europe "
        // I take what google gives me cause it's way better than myugly regex
        // might aswell take everything from google at this point
        $tempAdress = $route_name; // Here: "Place de l'Europe" (whithout whitespcae at the end)
        $adressExploded = explode(" ",$tempAdress);
        $intitule_voie = $adressExploded[0]; // "Place"
        $intitule_voie = str_replace('Ave.', 'AVENUE', $intitule_voie);

        // We search in ddb the "intitulevoie" that match with what we get from the "dossier"
        $q = $em->getRepository('cpossibleBundle:DbaIntitulevoie')->createQueryBuilder('v');
        $q->andWhere('v.intitulevoieNom LIKE :intitulevoieNom')
          ->setParameter('intitulevoieNom', $intitule_voie);
        $result = $q->getQuery();
        // Here we want to get the nom de voie as we wish to put in ddb like "PL"
        $arrayDDB = $result->getArrayResult(); // array of 1 array coming from ddb searching via infos
        $voie = $arrayDDB[0]['intitulevoieCode']; // here we get the "PL"
        $fulladress = "";
        $fulladress .= $voie;
        for ($i=1; $i < count($adressExploded) ; $i++) {
          $fulladress .= " " .strtoupper($adressExploded[$i]);
        }
        $fulladress = str_replace('DR', 'DOCTEUR', $fulladress);

        // Set the object
        $erp->setListeerpNomVoie($fulladress);
        // Check if $numeroVoie is the same as the one that google map gives us
        ($numeroVoie != $street_number)? $erp->setListeerpNumeroVoie($street_number) : $erp->setListeerpNumeroVoie($numeroVoie);
        // Check if i get the same postal code as google
        ($codePostal != $postal_code)? $erp->setListeerpCodePostal($postal_code) : $erp->setListeerpCodePostal($codePostal);
        $erp->setListeerpNomCommune($nom_commune);
        $erp->setListeerpCodeInsee($insee);
        // ($commune != $commune_google)? $erp->setListeerpNomCommune($commune_google) : $erp->setListeerpNomCommune($commune);
        $erp->setListeerpLatitude($location->lat);
        $erp->setListeerpLongitude($location->lng);
      }
      if ($libelle == "Nom de l'entreprise") {
        $erp->setListeerpDemandeur($value);
      }
      // For now status is always 0:
      $erp->setListeerpStatut(0);
    }
      $erp->setListeerpTypedossier($declaration);
      if ($declaration == "attestation") {
        // this hole process is to have an idAdap that looks like 'AC-69-T7534' and that we increment everytime
        // starting from the last id-adap entered that looked like 'AC-69-7533'. So we take the last one incremented and // add one

        $test = 'AC-'.$erp->getListeerpDepartement().'-';
        $qb = $em->getRepository('cpossibleBundle:DbaListeerp')->createQueryBuilder('erp');
        $qb->andWhere('erp.listeerpIdAdap LIKE :test')
          ->setParameter('test', '%'.$test.'%' )->orderBy('erp.listeerpIdAdap', 'DESC');
        $result = $qb->getQuery()->getResult()[0]->getListeerpIdAdap();

        $number = str_replace('AC-'.$erp->getListeerpDepartement().'-', '', $result);
        if (is_numeric($number)) {
          $number = intval($number);
        } else {
          $number = intval(str_replace('AC-'.$erp->getListeerpDepartement().'-T-', '', $result));
        }
        $idAdap = 'AC-'.$erp->getListeerpDepartement().'-T-'.($number+1);
        $erp->setListeerpIdAdap($idAdap);
      }

    $em->persist($erp);
    $em->flush();
    // return the id so i can count something in an simple array later
    return $erp->getListeerpId();
  }
}
