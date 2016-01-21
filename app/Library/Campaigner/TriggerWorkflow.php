<?php
namespace App\Library\Campaigner;
class TriggerWorkflow
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $workflowId
     */
    protected $workflowId = null;

    /**
     * @var string $xmlContactQuery
     */
    protected $xmlContactQuery = null;

    /**
     * @param Authentication $authentication
     * @param int $workflowId
     * @param string $xmlContactQuery
     */
    public function __construct($authentication, $workflowId, $xmlContactQuery)
    {
      $this->authentication = $authentication;
      $this->workflowId = $workflowId;
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
     * @return TriggerWorkflow
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getWorkflowId()
    {
      return $this->workflowId;
    }

    /**
     * @param int $workflowId
     * @return TriggerWorkflow
     */
    public function setWorkflowId($workflowId)
    {
      $this->workflowId = $workflowId;
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
     * @return TriggerWorkflow
     */
    public function setXmlContactQuery($xmlContactQuery)
    {
      $this->xmlContactQuery = $xmlContactQuery;
      return $this;
    }

}
