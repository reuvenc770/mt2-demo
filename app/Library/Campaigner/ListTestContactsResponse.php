<?php
namespace App\Library\Campaigner;
class ListTestContactsResponse
{

    /**
     * @var ArrayOfTestContact $ListTestContactsResult
     */
    protected $ListTestContactsResult = null;

    /**
     * @param ArrayOfTestContact $ListTestContactsResult
     */
    public function __construct($ListTestContactsResult)
    {
      $this->ListTestContactsResult = $ListTestContactsResult;
    }

    /**
     * @return ArrayOfTestContact
     */
    public function getListTestContactsResult()
    {
      return $this->ListTestContactsResult;
    }

    /**
     * @param ArrayOfTestContact $ListTestContactsResult
     * @return ListTestContactsResponse
     */
    public function setListTestContactsResult($ListTestContactsResult)
    {
      $this->ListTestContactsResult = $ListTestContactsResult;
      return $this;
    }

}
