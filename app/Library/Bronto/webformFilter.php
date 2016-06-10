<?php
namespace App\Library\Bronto;
class webformFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var string[] $id
     */
    protected $id = null;

    /**
     * @var stringValue[] $name
     */
    protected $name = null;

    /**
     * @var string[] $webformType
     */
    protected $webformType = null;

    /**
     * @param filterType $type
     */
    public function __construct($type)
    {
      $this->type = $type;
    }

    /**
     * @return filterType
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param filterType $type
     * @return webformFilter
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string[] $id
     * @return webformFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param stringValue[] $name
     * @return webformFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getWebformType()
    {
      return $this->webformType;
    }

    /**
     * @param string[] $webformType
     * @return webformFilter
     */
    public function setWebformType(array $webformType)
    {
      $this->webformType = $webformType;
      return $this;
    }

}
