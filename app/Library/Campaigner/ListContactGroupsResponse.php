<?php
namespace App\Library\Campaigner;
class ListContactGroupsResponse
{

    /**
     * @var ArrayOfContactGroupDescription $ListContactGroupsResult
     */
    protected $ListContactGroupsResult = null;

    /**
     * @param ArrayOfContactGroupDescription $ListContactGroupsResult
     */
    public function __construct($ListContactGroupsResult)
    {
      $this->ListContactGroupsResult = $ListContactGroupsResult;
    }

    /**
     * @return ArrayOfContactGroupDescription
     */
    public function getListContactGroupsResult()
    {
      return $this->ListContactGroupsResult;
    }

    /**
     * @param ArrayOfContactGroupDescription $ListContactGroupsResult
     * @return ListContactGroupsResponse
     */
    public function setListContactGroupsResult($ListContactGroupsResult)
    {
      $this->ListContactGroupsResult = $ListContactGroupsResult;
      return $this;
    }

}
