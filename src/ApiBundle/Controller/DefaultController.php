<?php

namespace ApiBundle\Controller;

use OrderBundle\Entity\OrderItem;
use OrderBundle\Entity\ProductOrder;
use ProductBundle\Entity\OrderActualizationError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $order = new ProductOrder();
        $entityManager->beginTransaction();
        foreach ( $requestedItems as $requestedItem ) {
            if (
                empty($requestedItem['id'])
                || empty($requestedItem['amount'])
                || !is_integer($requestedItem['id'])
                || !is_integer($requestedItem['amount'])
                ) {
                return new JsonResponse('Each and every order item must contain id and amount fields, and both of these fields should be integers!', Response::HTTP_BAD_REQUEST);
            }
            $product = $entityManager->getRepository('ProductBundle\Entity\Product')->find( $requestedItem['id'] );
            if ( $product === null ) {
                return new JsonResponse('Product not exists with the following id: #'.$requestedItem['id'].'!', Response::HTTP_BAD_REQUEST);
            }
            $orderItem = new OrderItem();
            $orderItem
                ->setProduct(  $product )
                ->setQuantity( $requestedItem['amount'] )
                ->setProductOrder( $order )
            ;
            $entityManager->persist($orderItem);
            $order->addOrderItem( $orderItem );
        }
        $entityManager->persist($order);
        $orderErrors = $entityManager->getRepository('ProductBundle\Entity\Product')->actualizeOrder( $order );
        if (!empty($orderErrors)) {
            return new JsonResponse($this->OrderErrorsToResponseContent( $orderErrors ), Response::HTTP_CONFLICT);
        }
        $entityManager->flush();
        $entityManager->commit();
        return new JsonResponse();
    }

    /**
     * @param OrderActualizationError[] $errors
     * @return string
     */
    private function OrderErrorsToResponseContent( array $errors ): array
    {
        $encodableArray = [];
        foreach ($errors as $error) {
            $encodableArray[] = [
                'product-id'    => $error->getProduct()->getId(),
                'error-message' => $error->getErrorMessage(),
            ];
        }
        return $encodableArray;
    }


}
