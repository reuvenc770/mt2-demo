<?php

class deliveryProductObject
{

    /**
     * @var string $placeholder
     */
    protected $placeholder = null;

    /**
     * @var string $productId
     */
    protected $productId = null;

    /**
     * @param string $placeholder
     * @param string $productId
     */
    public function __construct($placeholder, $productId)
    {
      $this->placeholder = $placeholder;
      $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
      return $this->placeholder;
    }

    /**
     * @param string $placeholder
     * @return deliveryProductObject
     */
    public function setPlaceholder($placeholder)
    {
      $this->placeholder = $placeholder;
      return $this;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
      return $this->productId;
    }

    /**
     * @param string $productId
     * @return deliveryProductObject
     */
    public function setProductId($productId)
    {
      $this->productId = $productId;
      return $this;
    }

}
