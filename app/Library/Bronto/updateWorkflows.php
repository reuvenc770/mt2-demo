<?php
namespace App\Library\Bronto;
class updateWorkflows
{

    /**
     * @var workflowObject[] $workflows
     */
    protected $workflows = null;

    /**
     * @param workflowObject[] $workflows
     */
    public function __construct(array $workflows)
    {
      $this->workflows = $workflows;
    }

    /**
     * @return workflowObject[]
     */
    public function getWorkflows()
    {
      return $this->workflows;
    }

    /**
     * @param workflowObject[] $workflows
     * @return updateWorkflows
     */
    public function setWorkflows(array $workflows)
    {
      $this->workflows = $workflows;
      return $this;
    }

}
