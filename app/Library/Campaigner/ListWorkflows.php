<?php
namespace App\Library\Campaigner;
class ListWorkflows
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var boolean $onlyApiTriggered
     */
    protected $onlyApiTriggered = null;

    /**
     * @var boolean $onlyActiveAndTest
     */
    protected $onlyActiveAndTest = null;

    /**
     * @param Authentication $authentication
     * @param boolean $onlyApiTriggered
     * @param boolean $onlyActiveAndTest
     */
    public function __construct($authentication, $onlyApiTriggered, $onlyActiveAndTest)
    {
      $this->authentication = $authentication;
      $this->onlyApiTriggered = $onlyApiTriggered;
      $this->onlyActiveAndTest = $onlyActiveAndTest;
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
     * @return ListWorkflows
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOnlyApiTriggered()
    {
      return $this->onlyApiTriggered;
    }

    /**
     * @param boolean $onlyApiTriggered
     * @return ListWorkflows
     */
    public function setOnlyApiTriggered($onlyApiTriggered)
    {
      $this->onlyApiTriggered = $onlyApiTriggered;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOnlyActiveAndTest()
    {
      return $this->onlyActiveAndTest;
    }

    /**
     * @param boolean $onlyActiveAndTest
     * @return ListWorkflows
     */
    public function setOnlyActiveAndTest($onlyActiveAndTest)
    {
      $this->onlyActiveAndTest = $onlyActiveAndTest;
      return $this;
    }

}
