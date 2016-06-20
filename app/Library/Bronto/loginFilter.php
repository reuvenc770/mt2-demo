<?php

class loginFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var stringValue[] $username
     */
    protected $username = null;

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
     * @return loginFilter
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getUsername()
    {
      return $this->username;
    }

    /**
     * @param stringValue[] $username
     * @return loginFilter
     */
    public function setUsername(array $username)
    {
      $this->username = $username;
      return $this;
    }

}
