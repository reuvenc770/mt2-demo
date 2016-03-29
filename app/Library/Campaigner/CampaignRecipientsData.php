<?php
namespace App\Library\Campaigner;
class CampaignRecipientsData
{

    /**
     * @var boolean $sendToAllContacts
     */
    protected $sendToAllContacts = null;

    /**
     * @var ArrayOfInt $contactGroupIds
     */
    protected $contactGroupIds = null;

    /**
     * @param boolean $sendToAllContacts
     * @param ArrayOfInt $contactGroupIds
     */
    public function __construct($sendToAllContacts, $contactGroupIds)
    {
      $this->sendToAllContacts = $sendToAllContacts;
      $this->contactGroupIds = $contactGroupIds;
    }

    /**
     * @return boolean
     */
    public function getSendToAllContacts()
    {
      return $this->sendToAllContacts;
    }

    /**
     * @param boolean $sendToAllContacts
     * @return CampaignRecipientsData
     */
    public function setSendToAllContacts($sendToAllContacts)
    {
      $this->sendToAllContacts = $sendToAllContacts;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getContactGroupIds()
    {
      return $this->contactGroupIds;
    }

    /**
     * @param ArrayOfInt $contactGroupIds
     * @return CampaignRecipientsData
     */
    public function setContactGroupIds($contactGroupIds)
    {
      $this->contactGroupIds = $contactGroupIds;
      return $this;
    }

}
