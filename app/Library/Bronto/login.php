<?php
namespace App\Library\Bronto;
class login
{

    /**
     * @var string $apiToken
     */
    protected $apiToken = null;

    /**
     * @param string $apiToken
     */
    public function __construct($apiToken)
    {
      $this->apiToken = $apiToken;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
      return $this->apiToken;
    }

    /**
     * @param string $apiToken
     * @return login
     */
    public function setApiToken($apiToken)
    {
      $this->apiToken = $apiToken;
      return $this;
    }

}
