<?php
namespace App\Library\Campaigner;
class ArrayOfUploadResultData
{

    /**
     * @var UploadResultData[] $UploadResultData
     */
    protected $UploadResultData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return UploadResultData[]
     */
    public function getUploadResultData()
    {
      return $this->UploadResultData;
    }

    /**
     * @param UploadResultData[] $UploadResultData
     * @return ArrayOfUploadResultData
     */
    public function setUploadResultData(array $UploadResultData)
    {
      $this->UploadResultData = $UploadResultData;
      return $this;
    }

}
