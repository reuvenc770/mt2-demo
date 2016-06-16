<?php

class contentTagObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $value
     */
    protected $value = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $value
     */
    public function __construct($id, $name, $value)
    {
      $this->id = $id;
      $this->name = $name;
      $this->value = $value;
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
     * @return contentTagObject
     */
    public function setId($id)
    {
      $this->id = $id;
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
     * @return contentTagObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
      return $this->value;
    }

    /**
     * @param string $value
     * @return contentTagObject
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

}
