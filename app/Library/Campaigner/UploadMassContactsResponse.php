<?php
namespace App\Library\Campaigner;
class UploadMassContactsResponse
{

    /**
     * @var UploadMassContactsResult $UploadMassContactsResult
     */
    protected $UploadMassContactsResult = null;

    /**
     * @param UploadMassContactsResult $UploadMassContactsResult
     */
    public function __construct($UploadMassContactsResult)
    {
      $this->UploadMassContactsResult = $UploadMassContactsResult;
    }

    /**
     * @return UploadMassContactsResult
     */
    public function getUploadMassContactsResult()
    {
      return $this->UploadMassContactsResult;
    }

    /**
     * @param UploadMassContactsResult $UploadMassContactsResult
     * @return UploadMassContactsResponse
     */
    public function setUploadMassContactsResult($UploadMassContactsResult)
    {
      $this->UploadMassContactsResult = $UploadMassContactsResult;
      return $this;
    }

}
