<?php
namespace App\Library\Campaigner;
class DeleteContacts
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ArrayOfContactKey $contactKeys
     */
    protected $contactKeys = null;

    /**
     * @param Authentication $authentication
     * @param ArrayOfContactKey $contactKeys
     */
    public function __construct($authentication, $contactKeys)
    {
      $this->authentication = $authentication;
      $this->contactKeys = $contactKeys;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return DeleteContacts
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ArrayOfContactKey
     */
    public function getContactKeys()
    {
      return $this->contactKeys;
    }

    /**
     * @param ArrayOfContactKey $contactKeys
     * @return DeleteContacts
     */
    public function setContactKeys($contactKeys)
    {
      $this->contactKeys = $contactKeys;
      return $this;
    }

}
