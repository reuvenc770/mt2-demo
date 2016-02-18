<?php
namespace App\Library\Campaigner;
class ArrayOfContactData
{

    /**
     * @var ContactData[] $ContactData
     */
    protected $ContactData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ContactData[]
     */
    public function getContactData()
    {
      return $this->ContactData;
    }

    /**
     * @param ContactData[] $ContactData
     * @return ArrayOfContactData
     */
    public function setContactData(array $ContactData)
    {
      $this->ContactData = $ContactData;
      return $this;
    }

}
