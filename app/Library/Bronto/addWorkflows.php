<?php
namespace App\Library\Bronto;
class addWorkflows
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
     * @return addWorkflows
     */
    public function setWorkflows(array $workflows)
    {
      $this->workflows = $workflows;
      return $this;
    }

}
