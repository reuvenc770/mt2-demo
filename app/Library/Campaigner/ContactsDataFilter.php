<?php
namespace App\Library\Campaigner;
class ContactsDataFilter
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
     * @return ContactsDataFilter
     */
    public function setContactKeys($ContactKeys)
    {
      $this->ContactKeys = $ContactKeys;
      return $this;
    }

}
