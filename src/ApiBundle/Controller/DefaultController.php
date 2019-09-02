<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route(
     *     "/api/",
     *      methods={"GET"}
     * )
     */
    public function listAction(Request $request)
    {
        return new Response('Api documentation');
    }

}
