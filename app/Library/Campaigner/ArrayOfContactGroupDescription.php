<?php
namespace App\Library\Campaigner;

class ArrayOfContactGroupDescription
{

    /**
     * @var ContactGroupDescription[] $ContactGroupDescription
     */
    protected $ContactGroupDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ContactGroupDescription[]
     */
    public function getContactGroupDescription()
    {
      return $this->ContactGroupDescription;
    }

    /**
     * @param ContactGroupDescription[] $ContactGroupDescription
     * @return ArrayOfContactGroupDescription
     */
    public function setContactGroupDescription(array $ContactGroupDescription)
    {
      $this->ContactGroupDescription = $ContactGroupDescription;
      return $this;
    }

}
