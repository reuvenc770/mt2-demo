<?php
namespace App\Library\Campaigner;
class ArrayOfContactKey
{

    /**
     * @var ContactKey[] $ContactKey
     */
    protected $ContactKey = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ContactKey[]
     */
    public function getContactKey()
    {
      return $this->ContactKey;
    }

    /**
     * @param ContactKey[] $ContactKey
     * @return ArrayOfContactKey
     */
    public function setContactKey(array $ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

}
