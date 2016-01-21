<?php
namespace App\Library\Campaigner;
class UploadMediaFileResponse
{

    /**
     * @var UploadMediaFileResult $UploadMediaFileResult
     */
    protected $UploadMediaFileResult = null;

    /**
     * @param UploadMediaFileResult $UploadMediaFileResult
     */
    public function __construct($UploadMediaFileResult)
    {
      $this->UploadMediaFileResult = $UploadMediaFileResult;
    }

    /**
     * @return UploadMediaFileResult
     */
    public function getUploadMediaFileResult()
    {
      return $this->UploadMediaFileResult;
    }

    /**
     * @param UploadMediaFileResult $UploadMediaFileResult
     * @return UploadMediaFileResponse
     */
    public function setUploadMediaFileResult($UploadMediaFileResult)
    {
      $this->UploadMediaFileResult = $UploadMediaFileResult;
      return $this;
    }

}
