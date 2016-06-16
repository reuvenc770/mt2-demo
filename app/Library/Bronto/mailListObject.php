<?php

class mailListObject
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
     * @var int $activeCount
     */
    protected $activeCount = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $visibility
     */
    protected $visibility = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $label
     * @param int $activeCount
     * @param string $status
     * @param string $visibility
     */
    public function __construct($id, $name, $label, $activeCount, $status, $visibility)
    {
      $this->id = $id;
      $this->name = $name;
      $this->label = $label;
      $this->activeCount = $activeCount;
      $this->status = $status;
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
     * @return mailListObject
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
     * @return mailListObject
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
     * @return mailListObject
     */
    public function setLabel($label)
    {
      $this->label = $label;
      return $this;
    }

    /**
     * @return int
     */
    public function getActiveCount()
    {
      return $this->activeCount;
    }

    /**
     * @param int $activeCount
     * @return mailListObject
     */
    public function setActiveCount($activeCount)
    {
      $this->activeCount = $activeCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return mailListObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
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
     * @return mailListObject
     */
    public function setVisibility($visibility)
    {
      $this->visibility = $visibility;
      return $this;
    }

}
