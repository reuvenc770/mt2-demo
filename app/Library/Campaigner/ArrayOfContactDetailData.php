<?php
namespace App\Library\Campaigner;
class ArrayOfContactDetailData
{

    /**
     * @var ContactDetailData[] $ContactDetailData
     */
    protected $ContactDetailData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ContactDetailData[]
     */
    public function getContactDetailData()
    {
      return $this->ContactDetailData;
    }

    /**
     * @param ContactDetailData[] $ContactDetailData
     * @return ArrayOfContactDetailData
     */
    public function setContactDetailData(array $ContactDetailData)
    {
      $this->ContactDetailData = $ContactDetailData;
      return $this;
    }

}
