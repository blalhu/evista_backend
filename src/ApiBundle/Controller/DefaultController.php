<?php

namespace ApiBundle\Controller;

use OrderBundle\Entity\OrderItem;
use OrderBundle\Entity\ProductOrder;
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
        $requestedItems = json_decode(
            $request->getContent(),
            true
        );
        $requestedItems = $requestedItems['items'];
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $order = new ProductOrder();
        $entityManager->beginTransaction();
        foreach ( $requestedItems as $requestedItem ) {
            $product = $entityManager->getRepository('ProductBundle\Entity\Product')->find( $requestedItem['id'] );
            //TODO: check the quantities here!
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
        $entityManager->getRepository('ProductBundle\Entity\Product')->actualizeOrder( $order );
        $entityManager->flush();
        $entityManager->commit();
        return new JsonResponse();
    }


}
