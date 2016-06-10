<?php

class headerFooterFilter
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
     * @var string[] $position
     */
    protected $position = null;

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
     * @return headerFooterFilter
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
     * @return headerFooterFilter
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
     * @return headerFooterFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getPosition()
    {
      return $this->position;
    }

    /**
     * @param string[] $position
     * @return headerFooterFilter
     */
    public function setPosition(array $position)
    {
      $this->position = $position;
      return $this;
    }

}
