<?php

namespace OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductOrder
 *
 * @ORM\Table(name="product_orders")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\ProductOrderRepository")
 */
class ProductOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var OrderItem[]
     *
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrderItem", mappedBy="productOrder")
     */
    private $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function addOrderItem( OrderItem $orderItem )
    {
        $this->orderItems->add( $orderItem );
    }

    /**
     * Get orderItems.
     *
     * @return OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }
}
