<?php

namespace OrderBundle\Controller;

use OrderBundle\Entity\ProductOrder;
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
     *     "/api/orders/{orderId}",
     *      methods={"GET"}
     * )
     */
    public function getOrderAction( $orderId )
    {
        /**
         * @var ProductOrder $order
         */
        $order = $this
            ->get('doctrine.orm.entity_manager')
            ->getRepository('OrderBundle\Entity\ProductOrder')
            ->find($orderId)
        ;
        if ( $order === null ) {
            return new JsonResponse('Order not found!', Response::HTTP_NOT_FOUND);
        }
        $orderedItems = [];
        foreach ( $order->getOrderItems() as $orderItem ) {
            $orderedItems[] = [
                'product-id'   => $orderItem->getProduct()->getId(),
                'product-name' => $orderItem->getProduct()->getName(),
                'amount'       => $orderItem->getQuantity(),
            ];
        }
        return new JsonResponse( $orderedItems );
    }

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
            $orderId = $processor->record($requestedItems);
        } catch ( BadRequestHttpException $e ) {
            return new JsonResponse($processor->getLastError(), Response::HTTP_BAD_REQUEST);
        } catch ( ConflictHttpException $e ) {
            return new JsonResponse($processor->getLastError(), Response::HTTP_CONFLICT);
        }
        return $this->forward('OrderBundle:Default:getOrder', ['orderId' => $orderId]);
    }

}
