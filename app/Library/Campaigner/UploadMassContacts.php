<?php
namespace App\Library\Campaigner;
class UploadMassContacts
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var boolean $UpdateExistingContacts
     */
    protected $UpdateExistingContacts = null;

    /**
     * @var boolean $TriggerWorkflow
     */
    protected $TriggerWorkflow = null;

    /**
     * @var ArrayOfContactData $contacts
     */
    protected $contacts = null;

    /**
     * @var ArrayOfInt $globalAddToGroup
     */
    protected $globalAddToGroup = null;

    /**
     * @var ArrayOfInt $globalRemoveFromGroup
     */
    protected $globalRemoveFromGroup = null;

    /**
     * @param Authentication $authentication
     * @param boolean $UpdateExistingContacts
     * @param boolean $TriggerWorkflow
     * @param ArrayOfContactData $contacts
     * @param ArrayOfInt $globalAddToGroup
     * @param ArrayOfInt $globalRemoveFromGroup
     */
    public function __construct($authentication, $UpdateExistingContacts, $TriggerWorkflow, $contacts, $globalAddToGroup, $globalRemoveFromGroup)
    {
      $this->authentication = $authentication;
      $this->UpdateExistingContacts = $UpdateExistingContacts;
      $this->TriggerWorkflow = $TriggerWorkflow;
      $this->contacts = $contacts;
      $this->globalAddToGroup = $globalAddToGroup;
      $this->globalRemoveFromGroup = $globalRemoveFromGroup;
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
     * @return UploadMassContacts
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getUpdateExistingContacts()
    {
      return $this->UpdateExistingContacts;
    }

    /**
     * @param boolean $UpdateExistingContacts
     * @return UploadMassContacts
     */
    public function setUpdateExistingContacts($UpdateExistingContacts)
    {
      $this->UpdateExistingContacts = $UpdateExistingContacts;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTriggerWorkflow()
    {
      return $this->TriggerWorkflow;
    }

    /**
     * @param boolean $TriggerWorkflow
     * @return UploadMassContacts
     */
    public function setTriggerWorkflow($TriggerWorkflow)
    {
      $this->TriggerWorkflow = $TriggerWorkflow;
      return $this;
    }

    /**
     * @return ArrayOfContactData
     */
    public function getContacts()
    {
      return $this->contacts;
    }

    /**
     * @param ArrayOfContactData $contacts
     * @return UploadMassContacts
     */
    public function setContacts($contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getGlobalAddToGroup()
    {
      return $this->globalAddToGroup;
    }

    /**
     * @param ArrayOfInt $globalAddToGroup
     * @return UploadMassContacts
     */
    public function setGlobalAddToGroup($globalAddToGroup)
    {
      $this->globalAddToGroup = $globalAddToGroup;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getGlobalRemoveFromGroup()
    {
      return $this->globalRemoveFromGroup;
    }

    /**
     * @param ArrayOfInt $globalRemoveFromGroup
     * @return UploadMassContacts
     */
    public function setGlobalRemoveFromGroup($globalRemoveFromGroup)
    {
      $this->globalRemoveFromGroup = $globalRemoveFromGroup;
      return $this;
    }

}
