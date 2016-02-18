<?php
namespace App\Library\Campaigner;
class CreateUpdateContactGroupsResponse
{

    /**
     * @var CreateUpdateContactGroupsResult $CreateUpdateContactGroupsResult
     */
    protected $CreateUpdateContactGroupsResult = null;

    /**
     * @param CreateUpdateContactGroupsResult $CreateUpdateContactGroupsResult
     */
    public function __construct($CreateUpdateContactGroupsResult)
    {
      $this->CreateUpdateContactGroupsResult = $CreateUpdateContactGroupsResult;
    }

    /**
     * @return CreateUpdateContactGroupsResult
     */
    public function getCreateUpdateContactGroupsResult()
    {
      return $this->CreateUpdateContactGroupsResult;
    }

    /**
     * @param CreateUpdateContactGroupsResult $CreateUpdateContactGroupsResult
     * @return CreateUpdateContactGroupsResponse
     */
    public function setCreateUpdateContactGroupsResult($CreateUpdateContactGroupsResult)
    {
      $this->CreateUpdateContactGroupsResult = $CreateUpdateContactGroupsResult;
      return $this;
    }

}
