<?php
namespace App\Library\Campaigner;
class ArrayOfInvalidContactDetailData
{

    /**
     * @var InvalidContactDetailData[] $InvalidContactDetailData
     */
    protected $InvalidContactDetailData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return InvalidContactDetailData[]
     */
    public function getInvalidContactDetailData()
    {
      return $this->InvalidContactDetailData;
    }

    /**
     * @param InvalidContactDetailData[] $InvalidContactDetailData
     * @return ArrayOfInvalidContactDetailData
     */
    public function setInvalidContactDetailData(array $InvalidContactDetailData)
    {
      $this->InvalidContactDetailData = $InvalidContactDetailData;
      return $this;
    }

}
