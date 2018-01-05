<?php
namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ERPController extends AbstractErpController
{
    public function autocomplete_nameAction(Request $request)
    {
        $entities = $this->getErpByType('listeErpNomErp', $request->get('term'));
        $erps = $this->parseErpEntities($entities);
        $response = new JsonResponse();
        $response->setData($erps);
        return $response;
    }
/*
    public function autocomplete_adressAction(Request $request)
    {
        $entities = $this->getErpByType('listeerpNomVoie', $request->get('term'));
        $erps = $this->parseErpEntities($entities);
        $response = new JsonResponse();
        $response->setData($erps);
        return $response;
    }
*/
}