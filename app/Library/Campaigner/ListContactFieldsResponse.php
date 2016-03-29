<?php
namespace App\Library\Campaigner;
class ListContactFieldsResponse
{

    /**
     * @var ArrayOfAttributeData $ListContactFieldsResult
     */
    protected $ListContactFieldsResult = null;

    /**
     * @param ArrayOfAttributeData $ListContactFieldsResult
     */
    public function __construct($ListContactFieldsResult)
    {
      $this->ListContactFieldsResult = $ListContactFieldsResult;
    }

    /**
     * @return ArrayOfAttributeData
     */
    public function getListContactFieldsResult()
    {
      return $this->ListContactFieldsResult;
    }

    /**
     * @param ArrayOfAttributeData $ListContactFieldsResult
     * @return ListContactFieldsResponse
     */
    public function setListContactFieldsResult($ListContactFieldsResult)
    {
      $this->ListContactFieldsResult = $ListContactFieldsResult;
      return $this;
    }

}
