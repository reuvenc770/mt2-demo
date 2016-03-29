<?php
namespace App\Library\Campaigner;
class UploadMassContactsStatus
{

    /**
     * @var UploadStatusData $UploadStatusData
     */
    protected $UploadStatusData = null;

    /**
     * @param UploadStatusData $UploadStatusData
     */
    public function __construct($UploadStatusData)
    {
      $this->UploadStatusData = $UploadStatusData;
    }

    /**
     * @return UploadStatusData
     */
    public function getUploadStatusData()
    {
      return $this->UploadStatusData;
    }

    /**
     * @param UploadStatusData $UploadStatusData
     * @return UploadMassContactsStatus
     */
    public function setUploadStatusData($UploadStatusData)
    {
      $this->UploadStatusData = $UploadStatusData;
      return $this;
    }

}
