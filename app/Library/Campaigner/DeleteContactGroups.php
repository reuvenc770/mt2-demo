<?php
namespace App\Library\Campaigner;
class DeleteContactGroups
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ArrayOfInt $ContactGroupIds
     */
    protected $ContactGroupIds = null;

    /**
     * @param Authentication $authentication
     * @param ArrayOfInt $ContactGroupIds
     */
    public function __construct($authentication, $ContactGroupIds)
    {
      $this->authentication = $authentication;
      $this->ContactGroupIds = $ContactGroupIds;
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
     * @return DeleteContactGroups
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getContactGroupIds()
    {
      return $this->ContactGroupIds;
    }

    /**
     * @param ArrayOfInt $ContactGroupIds
     * @return DeleteContactGroups
     */
    public function setContactGroupIds($ContactGroupIds)
    {
      $this->ContactGroupIds = $ContactGroupIds;
      return $this;
    }

}
