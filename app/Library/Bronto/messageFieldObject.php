<?php

class messageFieldObject
{

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @param string $name
     * @param string $type
     * @param string $content
     */
    public function __construct($name, $type, $content)
    {
      $this->name = $name;
      $this->type = $type;
      $this->content = $content;
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
     * @return messageFieldObject
     */
    public function setName($name)
    {
      $this->name = $name;
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
     * @return messageFieldObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param string $content
     * @return messageFieldObject
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

}
