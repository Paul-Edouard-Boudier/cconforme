<?php
namespace cpossibleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ERPController extends AbstractErpController
{
    public function autocomplete_nameAction(Request $request)
    {
        $entities = $this->getErpByType('listeErpNomErp', $_GET['term']);
        $erps = $this->parseErpEntities($entities);
        $response = new JsonResponse();
        $response->setData($erps);
        return $response;
    }
}