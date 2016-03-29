<?php
namespace App\Library\Campaigner;
class ListMediaFilesResponse
{

    /**
     * @var ArrayOfMediaFileDescription $ListMediaFilesResult
     */
    protected $ListMediaFilesResult = null;

    /**
     * @param ArrayOfMediaFileDescription $ListMediaFilesResult
     */
    public function __construct($ListMediaFilesResult)
    {
      $this->ListMediaFilesResult = $ListMediaFilesResult;
    }

    /**
     * @return ArrayOfMediaFileDescription
     */
    public function getListMediaFilesResult()
    {
      return $this->ListMediaFilesResult;
    }

    /**
     * @param ArrayOfMediaFileDescription $ListMediaFilesResult
     * @return ListMediaFilesResponse
     */
    public function setListMediaFilesResult($ListMediaFilesResult)
    {
      $this->ListMediaFilesResult = $ListMediaFilesResult;
      return $this;
    }

}
