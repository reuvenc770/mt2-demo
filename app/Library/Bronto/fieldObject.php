<?php

class fieldObject
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
     * @var string $label
     */
    protected $label = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $visibility
     */
    protected $visibility = null;

    /**
     * @var fieldOptionObject[] $options
     */
    protected $options = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $label
     * @param string $type
     * @param string $visibility
     */
    public function __construct($id, $name, $label, $type, $visibility)
    {
      $this->id = $id;
      $this->name = $name;
      $this->label = $label;
      $this->type = $type;
      $this->visibility = $visibility;
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
     * @return fieldObject
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
     * @return fieldObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
      return $this->label;
    }

    /**
     * @param string $label
     * @return fieldObject
     */
    public function setLabel($label)
    {
      $this->label = $label;
      return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return fieldObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
      return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return fieldObject
     */
    public function setVisibility($visibility)
    {
      $this->visibility = $visibility;
      return $this;
    }

    /**
     * @return fieldOptionObject[]
     */
    public function getOptions()
    {
      return $this->options;
    }

    /**
     * @param fieldOptionObject[] $options
     * @return fieldObject
     */
    public function setOptions(array $options)
    {
      $this->options = $options;
      return $this;
    }

}
