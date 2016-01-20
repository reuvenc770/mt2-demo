<?php
namespace App\Library\Campaigner;
class GetUploadMassContactsResultResponse
{

    /**
     * @var ArrayOfContactResultData $GetUploadMassContactsResultResult
     */
    protected $GetUploadMassContactsResultResult = null;

    /**
     * @param ArrayOfContactResultData $GetUploadMassContactsResultResult
     */
    public function __construct($GetUploadMassContactsResultResult)
    {
      $this->GetUploadMassContactsResultResult = $GetUploadMassContactsResultResult;
    }

    /**
     * @return ArrayOfContactResultData
     */
    public function getGetUploadMassContactsResultResult()
    {
      return $this->GetUploadMassContactsResultResult;
    }

    /**
     * @param ArrayOfContactResultData $GetUploadMassContactsResultResult
     * @return GetUploadMassContactsResultResponse
     */
    public function setGetUploadMassContactsResultResult($GetUploadMassContactsResultResult)
    {
      $this->GetUploadMassContactsResultResult = $GetUploadMassContactsResultResult;
      return $this;
    }

}
