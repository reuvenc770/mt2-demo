<?php
namespace App\Library\Campaigner;
class ArrayOfMediaFileDescription
{

    /**
     * @var MediaFileDescription[] $MediaFileDescription
     */
    protected $MediaFileDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return MediaFileDescription[]
     */
    public function getMediaFileDescription()
    {
      return $this->MediaFileDescription;
    }

    /**
     * @param MediaFileDescription[] $MediaFileDescription
     * @return ArrayOfMediaFileDescription
     */
    public function setMediaFileDescription(array $MediaFileDescription)
    {
      $this->MediaFileDescription = $MediaFileDescription;
      return $this;
    }

}
