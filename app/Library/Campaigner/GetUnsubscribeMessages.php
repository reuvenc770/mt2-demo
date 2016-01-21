<?php
namespace App\Library\Campaigner;
class GetUnsubscribeMessages
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @param Authentication $authentication
     */
    public function __construct($authentication)
    {
      $this->authentication = $authentication;
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
     * @return GetUnsubscribeMessages
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

}
