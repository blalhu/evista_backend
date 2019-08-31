<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route(
     *     "/api/orders/",
     *      methods={"GET"}
     * )
     */
    public function listAction(Request $request)
    {
        return new JsonResponse([]);
    }

    /**
     * @Route(
     *     "/api/orders/",
     *      methods={"POST"}
     * )
     */
    public function recordAction(Request $request)
    {
        return new JsonResponse();
    }


}
