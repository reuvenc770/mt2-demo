<?php
namespace App\Library\Campaigner;
class RunReport
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $xmlContactQuery
     */
    protected $xmlContactQuery = null;

    /**
     * @param Authentication $authentication
     * @param string $xmlContactQuery
     */
    public function __construct($authentication, $xmlContactQuery)
    {
      $this->authentication = $authentication;
      $this->xmlContactQuery = $xmlContactQuery;
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
     * @return RunReport
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return string
     */
    public function getXmlContactQuery()
    {
      return $this->xmlContactQuery;
    }

    /**
     * @param string $xmlContactQuery
     * @return RunReport
     */
    public function setXmlContactQuery($xmlContactQuery)
    {
      $this->xmlContactQuery = $xmlContactQuery;
      return $this;
    }

}
