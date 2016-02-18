<?php
namespace App\Library\Campaigner;
class ArrayOfWorkflowDescription
{

    /**
     * @var WorkflowDescription[] $WorkflowDescription
     */
    protected $WorkflowDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return WorkflowDescription[]
     */
    public function getWorkflowDescription()
    {
      return $this->WorkflowDescription;
    }

    /**
     * @param WorkflowDescription[] $WorkflowDescription
     * @return ArrayOfWorkflowDescription
     */
    public function setWorkflowDescription(array $WorkflowDescription)
    {
      $this->WorkflowDescription = $WorkflowDescription;
      return $this;
    }

}
