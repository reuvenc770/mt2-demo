<?php
namespace App\Library\Campaigner;
class ListEmailTemplates
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
     * @return ListEmailTemplates
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

}
