<?php
namespace App\Library\Campaigner;
class GetUploadMassContactsStatusResponse
{

    /**
     * @var UploadMassContactsStatus $GetUploadMassContactsStatusResult
     */
    protected $GetUploadMassContactsStatusResult = null;

    /**
     * @param UploadMassContactsStatus $GetUploadMassContactsStatusResult
     */
    public function __construct($GetUploadMassContactsStatusResult)
    {
      $this->GetUploadMassContactsStatusResult = $GetUploadMassContactsStatusResult;
    }

    /**
     * @return UploadMassContactsStatus
     */
    public function getGetUploadMassContactsStatusResult()
    {
      return $this->GetUploadMassContactsStatusResult;
    }

    /**
     * @param UploadMassContactsStatus $GetUploadMassContactsStatusResult
     * @return GetUploadMassContactsStatusResponse
     */
    public function setGetUploadMassContactsStatusResult($GetUploadMassContactsStatusResult)
    {
      $this->GetUploadMassContactsStatusResult = $GetUploadMassContactsStatusResult;
      return $this;
    }

}
