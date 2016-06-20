<?php

class messageFolderFilter
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
     * @var string[] $parentId
     */
    protected $parentId = null;

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
     * @return messageFolderFilter
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
     * @return messageFolderFilter
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
     * @return messageFolderFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getParentId()
    {
      return $this->parentId;
    }

    /**
     * @param string[] $parentId
     * @return messageFolderFilter
     */
    public function setParentId(array $parentId)
    {
      $this->parentId = $parentId;
      return $this;
    }

}
