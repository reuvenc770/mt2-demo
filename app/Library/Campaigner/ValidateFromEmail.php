<?php
namespace App\Library\Campaigner;
class ValidateFromEmail
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $email
     */
    protected $email = null;

    /**
     * @param Authentication $authentication
     * @param string $email
     */
    public function __construct($authentication, $email)
    {
      $this->authentication = $authentication;
      $this->email = $email;
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
     * @return ValidateFromEmail
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
      return $this->email;
    }

    /**
     * @param string $email
     * @return ValidateFromEmail
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

}
