<?php
namespace App\Library\Campaigner;
class ListWorkflowsResponse
{

    /**
     * @var ArrayOfWorkflowDescription $ListWorkflowsResult
     */
    protected $ListWorkflowsResult = null;

    /**
     * @param ArrayOfWorkflowDescription $ListWorkflowsResult
     */
    public function __construct($ListWorkflowsResult)
    {
      $this->ListWorkflowsResult = $ListWorkflowsResult;
    }

    /**
     * @return ArrayOfWorkflowDescription
     */
    public function getListWorkflowsResult()
    {
      return $this->ListWorkflowsResult;
    }

    /**
     * @param ArrayOfWorkflowDescription $ListWorkflowsResult
     * @return ListWorkflowsResponse
     */
    public function setListWorkflowsResult($ListWorkflowsResult)
    {
      $this->ListWorkflowsResult = $ListWorkflowsResult;
      return $this;
    }

}
