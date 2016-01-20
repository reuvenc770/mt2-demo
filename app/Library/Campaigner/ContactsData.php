<?php
namespace App\Library\Campaigner;
class ContactsData
{

    /**
     * @var ArrayOfContactDetailData $ContactData
     */
    protected $ContactData = null;

    /**
     * @var ArrayOfInvalidContactDetailData $InvalidContactData
     */
    protected $InvalidContactData = null;

    /**
     * @param ArrayOfContactDetailData $ContactData
     * @param ArrayOfInvalidContactDetailData $InvalidContactData
     */
    public function __construct($ContactData, $InvalidContactData)
    {
      $this->ContactData = $ContactData;
      $this->InvalidContactData = $InvalidContactData;
    }

    /**
     * @return ArrayOfContactDetailData
     */
    public function getContactData()
    {
      return $this->ContactData;
    }

    /**
     * @param ArrayOfContactDetailData $ContactData
     * @return ContactsData
     */
    public function setContactData($ContactData)
    {
      $this->ContactData = $ContactData;
      return $this;
    }

    /**
     * @return ArrayOfInvalidContactDetailData
     */
    public function getInvalidContactData()
    {
      return $this->InvalidContactData;
    }

    /**
     * @param ArrayOfInvalidContactDetailData $InvalidContactData
     * @return ContactsData
     */
    public function setInvalidContactData($InvalidContactData)
    {
      $this->InvalidContactData = $InvalidContactData;
      return $this;
    }

}
