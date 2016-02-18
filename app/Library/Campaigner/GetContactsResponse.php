<?php
namespace App\Library\Campaigner;
class GetContactsResponse
{

    /**
     * @var ContactsData $GetContactsResult
     */
    protected $GetContactsResult = null;

    /**
     * @param ContactsData $GetContactsResult
     */
    public function __construct($GetContactsResult)
    {
      $this->GetContactsResult = $GetContactsResult;
    }

    /**
     * @return ContactsData
     */
    public function getGetContactsResult()
    {
      return $this->GetContactsResult;
    }

    /**
     * @param ContactsData $GetContactsResult
     * @return GetContactsResponse
     */
    public function setGetContactsResult($GetContactsResult)
    {
      $this->GetContactsResult = $GetContactsResult;
      return $this;
    }

}
