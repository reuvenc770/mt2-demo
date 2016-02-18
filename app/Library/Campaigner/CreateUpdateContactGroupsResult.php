<?php
namespace App\Library\Campaigner;
class CreateUpdateContactGroupsResult
{

    /**
     * @var int $ContactGroupId
     */
    protected $ContactGroupId = null;

    /**
     * @param int $ContactGroupId
     */
    public function __construct($ContactGroupId)
    {
      $this->ContactGroupId = $ContactGroupId;
    }

    /**
     * @return int
     */
    public function getContactGroupId()
    {
      return $this->ContactGroupId;
    }

    /**
     * @param int $ContactGroupId
     * @return CreateUpdateContactGroupsResult
     */
    public function setContactGroupId($ContactGroupId)
    {
      $this->ContactGroupId = $ContactGroupId;
      return $this;
    }

}
