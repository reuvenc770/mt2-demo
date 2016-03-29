<?php
namespace App\Library\Campaigner;
class ImmediateUploadResponse
{

    /**
     * @var ArrayOfUploadResultData $ImmediateUploadResult
     */
    protected $ImmediateUploadResult = null;

    /**
     * @param ArrayOfUploadResultData $ImmediateUploadResult
     */
    public function __construct($ImmediateUploadResult)
    {
      $this->ImmediateUploadResult = $ImmediateUploadResult;
    }

    /**
     * @return ArrayOfUploadResultData
     */
    public function getImmediateUploadResult()
    {
      return $this->ImmediateUploadResult;
    }

    /**
     * @param ArrayOfUploadResultData $ImmediateUploadResult
     * @return ImmediateUploadResponse
     */
    public function setImmediateUploadResult($ImmediateUploadResult)
    {
      $this->ImmediateUploadResult = $ImmediateUploadResult;
      return $this;
    }

}
