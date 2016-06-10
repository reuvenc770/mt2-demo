<?php
namespace App\Library\Bronto;
class readWorkflowsResponse
{

    /**
     * @var workflowObject[] $return
     */
    protected $return = null;

    /**
     * @param workflowObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return workflowObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param workflowObject[] $return
     * @return readWorkflowsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
