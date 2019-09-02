<?php

namespace OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class DefaultController extends Controller
{

    /**
     * @Route(
     *     "/api/orders/",
     *      methods={"POST"}
     * )
     */
    public function recordAction(Request $request)
    {
        $requestedItems = json_decode(
            $request->getContent(),
            true
        );
        if ( $requestedItems === null ) {
            return new JsonResponse('Invalid JSON format!', Response::HTTP_BAD_REQUEST);
        }
        if ( empty($requestedItems['items']) || !is_array($requestedItems['items']) ) {
            return new JsonResponse('Items object is missing, empty or not an array!', Response::HTTP_BAD_REQUEST);
        }
        $requestedItems = $requestedItems['items'];
        $processor = $this->get('order.processor');
        try {
            $processor->record($requestedItems);
        } catch ( BadRequestHttpException $e ) {
            return new JsonResponse($processor->getLastError(), Response::HTTP_BAD_REQUEST);
        } catch ( ConflictHttpException $e ) {
            return new JsonResponse($processor->getLastError(), Response::HTTP_CONFLICT);
        }
        return new JsonResponse();
    }

}
