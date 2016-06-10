<?php

class deleteWorkflows
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
     * @return deleteWorkflows
     */
    public function setWorkflows(array $workflows)
    {
      $this->workflows = $workflows;
      return $this;
    }

}
