<?php

namespace ProductBundle\Controller;

use ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/api/products/")
     */
    public function listAction()
    {
        /**
         * @var Product[] $products
         */
        $products = $this
            ->get('doctrine.orm.entity_manager')
            ->getRepository('ProductBundle\Entity\Product')
            ->findAll()
        ;
        $returnData = [ 'products' => [] ];
        foreach ( $products as $product ) {
            $returnData['products'][] = [
                'product-id'       => $product->getId(),
                'name'             => $product->getName(),
                'available-amount' => $product->getAvailableQuantity(),
            ];
        }
        return new JsonResponse($returnData);
    }
}
