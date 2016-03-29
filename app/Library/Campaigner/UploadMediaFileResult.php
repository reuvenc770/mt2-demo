<?php
namespace App\Library\Campaigner;
class UploadMediaFileResult
{

    /**
     * @var UploadMediaFileData $MediaFileData
     */
    protected $MediaFileData = null;

    /**
     * @param UploadMediaFileData $MediaFileData
     */
    public function __construct($MediaFileData)
    {
      $this->MediaFileData = $MediaFileData;
    }

    /**
     * @return UploadMediaFileData
     */
    public function getMediaFileData()
    {
      return $this->MediaFileData;
    }

    /**
     * @param UploadMediaFileData $MediaFileData
     * @return UploadMediaFileResult
     */
    public function setMediaFileData($MediaFileData)
    {
      $this->MediaFileData = $MediaFileData;
      return $this;
    }

}
