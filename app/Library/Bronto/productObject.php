<?php
namespace App\Library\Bronto;
class productObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $sku
     */
    protected $sku = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var string $category
     */
    protected $category = null;

    /**
     * @var string $image
     */
    protected $image = null;

    /**
     * @var string $url
     */
    protected $url = null;

    /**
     * @var int $quantity
     */
    protected $quantity = null;

    /**
     * @var float $price
     */
    protected $price = null;

    /**
     * @param string $id
     * @param string $sku
     * @param string $name
     * @param string $description
     * @param string $category
     * @param string $image
     * @param string $url
     * @param int $quantity
     * @param float $price
     */
    public function __construct($id, $sku, $name, $description, $category, $image, $url, $quantity, $price)
    {
      $this->id = $id;
      $this->sku = $sku;
      $this->name = $name;
      $this->description = $description;
      $this->category = $category;
      $this->image = $image;
      $this->url = $url;
      $this->quantity = $quantity;
      $this->price = $price;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return productObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getSku()
    {
      return $this->sku;
    }

    /**
     * @param string $sku
     * @return productObject
     */
    public function setSku($sku)
    {
      $this->sku = $sku;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return productObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     * @return productObject
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
      return $this->category;
    }

    /**
     * @param string $category
     * @return productObject
     */
    public function setCategory($category)
    {
      $this->category = $category;
      return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
      return $this->image;
    }

    /**
     * @param string $image
     * @return productObject
     */
    public function setImage($image)
    {
      $this->image = $image;
      return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
      return $this->url;
    }

    /**
     * @param string $url
     * @return productObject
     */
    public function setUrl($url)
    {
      $this->url = $url;
      return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
      return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return productObject
     */
    public function setQuantity($quantity)
    {
      $this->quantity = $quantity;
      return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
      return $this->price;
    }

    /**
     * @param float $price
     * @return productObject
     */
    public function setPrice($price)
    {
      $this->price = $price;
      return $this;
    }

}
