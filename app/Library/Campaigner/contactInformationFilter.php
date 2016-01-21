<?php
namespace App\Library\Campaigner;
class contactInformationFilter
{

    /**
     * @var boolean $IncludeStaticAttributes
     */
    protected $IncludeStaticAttributes = null;

    /**
     * @var boolean $IncludeCustomAttributes
     */
    protected $IncludeCustomAttributes = null;

    /**
     * @var boolean $IncludeSystemAttributes
     */
    protected $IncludeSystemAttributes = null;

    /**
     * @var boolean $IncludeGroupMembershipData
     */
    protected $IncludeGroupMembershipData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return boolean
     */
    public function getIncludeStaticAttributes()
    {
      return $this->IncludeStaticAttributes;
    }

    /**
     * @param boolean $IncludeStaticAttributes
     * @return contactInformationFilter
     */
    public function setIncludeStaticAttributes($IncludeStaticAttributes)
    {
      $this->IncludeStaticAttributes = $IncludeStaticAttributes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeCustomAttributes()
    {
      return $this->IncludeCustomAttributes;
    }

    /**
     * @param boolean $IncludeCustomAttributes
     * @return contactInformationFilter
     */
    public function setIncludeCustomAttributes($IncludeCustomAttributes)
    {
      $this->IncludeCustomAttributes = $IncludeCustomAttributes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeSystemAttributes()
    {
      return $this->IncludeSystemAttributes;
    }

    /**
     * @param boolean $IncludeSystemAttributes
     * @return contactInformationFilter
     */
    public function setIncludeSystemAttributes($IncludeSystemAttributes)
    {
      $this->IncludeSystemAttributes = $IncludeSystemAttributes;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeGroupMembershipData()
    {
      return $this->IncludeGroupMembershipData;
    }

    /**
     * @param boolean $IncludeGroupMembershipData
     * @return contactInformationFilter
     */
    public function setIncludeGroupMembershipData($IncludeGroupMembershipData)
    {
      $this->IncludeGroupMembershipData = $IncludeGroupMembershipData;
      return $this;
    }

}
