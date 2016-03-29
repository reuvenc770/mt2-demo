<?php
namespace App\Library\Campaigner;
class ContactKeysWrapper
{

    /**
     * @var ArrayOfContactKey $ContactKeys
     */
    protected $ContactKeys = null;

    /**
     * @param ArrayOfContactKey $ContactKeys
     */
    public function __construct($ContactKeys)
    {
      $this->ContactKeys = $ContactKeys;
    }

    /**
     * @return ArrayOfContactKey
     */
    public function getContactKeys()
    {
      return $this->ContactKeys;
    }

    /**
     * @param ArrayOfContactKey $ContactKeys
     * @return ContactKeysWrapper
     */
    public function setContactKeys($ContactKeys)
    {
      $this->ContactKeys = $ContactKeys;
      return $this;
    }

}
