<?php
namespace App\Library\Bronto;
class addContactsToWorkflow
{

    /**
     * @var workflowObject $workflow
     */
    protected $workflow = null;

    /**
     * @var contactObject[] $contacts
     */
    protected $contacts = null;

    /**
     * @param workflowObject $workflow
     * @param contactObject[] $contacts
     */
    public function __construct($workflow, array $contacts)
    {
      $this->workflow = $workflow;
      $this->contacts = $contacts;
    }

    /**
     * @return workflowObject
     */
    public function getWorkflow()
    {
      return $this->workflow;
    }

    /**
     * @param workflowObject $workflow
     * @return addContactsToWorkflow
     */
    public function setWorkflow($workflow)
    {
      $this->workflow = $workflow;
      return $this;
    }

    /**
     * @return contactObject[]
     */
    public function getContacts()
    {
      return $this->contacts;
    }

    /**
     * @param contactObject[] $contacts
     * @return addContactsToWorkflow
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
