<?php


namespace OrderBundle\DependencyInjection;


use Doctrine\ORM\EntityManager;
use OrderBundle\Entity\OrderItem;
use OrderBundle\Entity\ProductOrder;
use ProductBundle\Entity\OrderActualizationError;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OrderProcessor
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    private $lastError = null;

    public function __construct( EntityManager $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $requestedItems
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     * @return int
     */
    public function record( $requestedItems ): int
    {
        $this->lastError = null;
        $order = new ProductOrder();
        $this->entityManager->beginTransaction();
        foreach ( $requestedItems as $requestedItem ) {
            $this->checkForStructureErrors( $requestedItem );
            $product = $this->entityManager->getRepository('ProductBundle\Entity\Product')->find( $requestedItem['id'] );
            if ( $product === null ) {
                $this->lastError = 'Product not exists with the following id: #'.$requestedItem['id'].'!';
                throw new BadRequestHttpException();
            }
            $orderItem = new OrderItem();
            $orderItem
                ->setProduct(  $product )
                ->setQuantity( $requestedItem['amount'] )
                ->setProductOrder( $order )
            ;
            $this->entityManager->persist($orderItem);
            $order->addOrderItem( $orderItem );
        }
        $this->entityManager->persist($order);
        $orderErrors = $this->entityManager->getRepository('ProductBundle\Entity\Product')->actualizeOrder( $order );
        if (!empty($orderErrors)) {
            $this->lastError = $this->orderErrorsToResponseContent($orderErrors);
            throw new ConflictHttpException();
        }
        $this->entityManager->flush();
        $this->entityManager->commit();
        return $order->getId();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param OrderActualizationError[] $errors
     * @return string
     */
    private function orderErrorsToResponseContent( array $errors ): array
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

    /**
     * @param $requestedItem
     * @throws BadRequestHttpException
     */
    private function checkForStructureErrors( $requestedItem )
    {
        if (
            empty($requestedItem['id'])
            || empty($requestedItem['amount'])
            || !is_integer($requestedItem['id'])
            || !is_integer($requestedItem['amount'])
        ) {
            $this->lastError = 'Each and every order item must contain id and amount fields, and both of these fields should be integers!';
            throw new BadRequestHttpException();
        }
    }

}
