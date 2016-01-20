<?php
namespace App\Library\Campaigner;
class ArrayOfUnsubscribeMessageData
{

    /**
     * @var UnsubscribeMessageData[] $UnsubscribeMessageData
     */
    protected $UnsubscribeMessageData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return UnsubscribeMessageData[]
     */
    public function getUnsubscribeMessageData()
    {
      return $this->UnsubscribeMessageData;
    }

    /**
     * @param UnsubscribeMessageData[] $UnsubscribeMessageData
     * @return ArrayOfUnsubscribeMessageData
     */
    public function setUnsubscribeMessageData(array $UnsubscribeMessageData)
    {
      $this->UnsubscribeMessageData = $UnsubscribeMessageData;
      return $this;
    }

}
