<?php


namespace ProductBundle\Entity;


class OrderActualizationError
{
    /**
     * @var Product $product
     */
    private $product;

    /**
     * @var string $errorMessage
     */
    private $errorMessage;

    /**
     * OrderActualizationError constructor.
     * @param Product $product
     * @param string $errorMessage
     */
    public function __construct(
        Product $product,
        string $errorMessage
    )
    {
        $this->product      = $product;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

}