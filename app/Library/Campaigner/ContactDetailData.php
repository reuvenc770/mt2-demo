<?php
namespace App\Library\Campaigner;
class ContactDetailData
{

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

    /**
     * @var StaticAttributes $StaticAttributes
     */
    protected $StaticAttributes = null;

    /**
     * @var SystemAttributes $SystemAttributes
     */
    protected $SystemAttributes = null;

    /**
     * @var ArrayOfAttributeDetails $CustomAttributes
     */
    protected $CustomAttributes = null;

    /**
     * @var ArrayOfContactGroupDescription $GroupMembershipData
     */
    protected $GroupMembershipData = null;

    /**
     * @param ContactKey $ContactKey
     * @param StaticAttributes $StaticAttributes
     * @param SystemAttributes $SystemAttributes
     * @param ArrayOfAttributeDetails $CustomAttributes
     * @param ArrayOfContactGroupDescription $GroupMembershipData
     */
    public function __construct($ContactKey, $StaticAttributes, $SystemAttributes, $CustomAttributes, $GroupMembershipData)
    {
      $this->ContactKey = $ContactKey;
      $this->StaticAttributes = $StaticAttributes;
      $this->SystemAttributes = $SystemAttributes;
      $this->CustomAttributes = $CustomAttributes;
      $this->GroupMembershipData = $GroupMembershipData;
    }

    /**
     * @return ContactKey
     */
    public function getContactKey()
    {
      return $this->ContactKey;
    }

    /**
     * @param ContactKey $ContactKey
     * @return ContactDetailData
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

    /**
     * @return StaticAttributes
     */
    public function getStaticAttributes()
    {
      return $this->StaticAttributes;
    }

    /**
     * @param StaticAttributes $StaticAttributes
     * @return ContactDetailData
     */
    public function setStaticAttributes($StaticAttributes)
    {
      $this->StaticAttributes = $StaticAttributes;
      return $this;
    }

    /**
     * @return SystemAttributes
     */
    public function getSystemAttributes()
    {
      return $this->SystemAttributes;
    }

    /**
     * @param SystemAttributes $SystemAttributes
     * @return ContactDetailData
     */
    public function setSystemAttributes($SystemAttributes)
    {
      $this->SystemAttributes = $SystemAttributes;
      return $this;
    }

    /**
     * @return ArrayOfAttributeDetails
     */
    public function getCustomAttributes()
    {
      return $this->CustomAttributes;
    }

    /**
     * @param ArrayOfAttributeDetails $CustomAttributes
     * @return ContactDetailData
     */
    public function setCustomAttributes($CustomAttributes)
    {
      $this->CustomAttributes = $CustomAttributes;
      return $this;
    }

    /**
     * @return ArrayOfContactGroupDescription
     */
    public function getGroupMembershipData()
    {
      return $this->GroupMembershipData;
    }

    /**
     * @param ArrayOfContactGroupDescription $GroupMembershipData
     * @return ContactDetailData
     */
    public function setGroupMembershipData($GroupMembershipData)
    {
      $this->GroupMembershipData = $GroupMembershipData;
      return $this;
    }

}
