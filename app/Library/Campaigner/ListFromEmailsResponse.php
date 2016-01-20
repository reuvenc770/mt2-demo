<?php
namespace App\Library\Campaigner;
class ListFromEmailsResponse
{

    /**
     * @var ArrayOfFromEmailDescription $ListFromEmailsResult
     */
    protected $ListFromEmailsResult = null;

    /**
     * @param ArrayOfFromEmailDescription $ListFromEmailsResult
     */
    public function __construct($ListFromEmailsResult)
    {
      $this->ListFromEmailsResult = $ListFromEmailsResult;
    }

    /**
     * @return ArrayOfFromEmailDescription
     */
    public function getListFromEmailsResult()
    {
      return $this->ListFromEmailsResult;
    }

    /**
     * @param ArrayOfFromEmailDescription $ListFromEmailsResult
     * @return ListFromEmailsResponse
     */
    public function setListFromEmailsResult($ListFromEmailsResult)
    {
      $this->ListFromEmailsResult = $ListFromEmailsResult;
      return $this;
    }

}
