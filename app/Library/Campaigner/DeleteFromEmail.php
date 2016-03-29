<?php
namespace App\Library\Campaigner;
class DeleteFromEmail
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $Email
     */
    protected $Email = null;

    /**
     * @param Authentication $authentication
     * @param string $Email
     */
    public function __construct($authentication, $Email)
    {
      $this->authentication = $authentication;
      $this->Email = $Email;
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
     * @return DeleteFromEmail
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
      return $this->Email;
    }

    /**
     * @param string $Email
     * @return DeleteFromEmail
     */
    public function setEmail($Email)
    {
      $this->Email = $Email;
      return $this;
    }

}
