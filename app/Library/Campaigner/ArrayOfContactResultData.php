<?php
namespace App\Library\Campaigner;
class ArrayOfContactResultData
{

    /**
     * @var ContactResultData[] $ContactResultData
     */
    protected $ContactResultData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ContactResultData[]
     */
    public function getContactResultData()
    {
      return $this->ContactResultData;
    }

    /**
     * @param ContactResultData[] $ContactResultData
     * @return ArrayOfContactResultData
     */
    public function setContactResultData(array $ContactResultData)
    {
      $this->ContactResultData = $ContactResultData;
      return $this;
    }

}
