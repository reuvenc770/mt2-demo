<?php
namespace App\Library\Campaigner;
class WorkflowManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'TriggerWorkflow' => '\\TriggerWorkflow',
      'Authentication' => '\\Authentication',
      'TriggerWorkflowResponse' => '\\TriggerWorkflowResponse',
      'ResponseHeader' => '\\ResponseHeader',
      'ListWorkflows' => '\\ListWorkflows',
      'ListWorkflowsResponse' => '\\ListWorkflowsResponse',
      'ArrayOfWorkflowDescription' => '\\ArrayOfWorkflowDescription',
      'WorkflowDescription' => '\\WorkflowDescription',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/workflowmanagement.asmx?WSDL')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      parent::__construct($wsdl, $options);
    }

    /**
     * Queue contacts, identified by the criteria specified by the ContactSearchCrireria Xml, to enter the specified workflow.
     *
     * @param TriggerWorkflow $parameters
     * @return TriggerWorkflowResponse
     */
    public function TriggerWorkflow(TriggerWorkflow $parameters)
    {
      return $this->__soapCall('TriggerWorkflow', array($parameters));
    }

    /**
     * Get array of workflows.
     *
     * @param ListWorkflows $parameters
     * @return ListWorkflowsResponse
     */
    public function ListWorkflows(ListWorkflows $parameters)
    {
      return $this->__soapCall('ListWorkflows', array($parameters));
    }

}
