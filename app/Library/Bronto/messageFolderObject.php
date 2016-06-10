<?php

class messageFolderObject
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
     * @var string $parentId
     */
    protected $parentId = null;

    /**
     * @var string $parentName
     */
    protected $parentName = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $parentId
     * @param string $parentName
     */
    public function __construct($id, $name, $parentId, $parentName)
    {
      $this->id = $id;
      $this->name = $name;
      $this->parentId = $parentId;
      $this->parentName = $parentName;
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
     * @return messageFolderObject
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
     * @return messageFolderObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
      return $this->parentId;
    }

    /**
     * @param string $parentId
     * @return messageFolderObject
     */
    public function setParentId($parentId)
    {
      $this->parentId = $parentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getParentName()
    {
      return $this->parentName;
    }

    /**
     * @param string $parentName
     * @return messageFolderObject
     */
    public function setParentName($parentName)
    {
      $this->parentName = $parentName;
      return $this;
    }

}
