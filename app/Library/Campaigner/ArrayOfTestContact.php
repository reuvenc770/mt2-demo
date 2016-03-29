<?php
namespace App\Library\Campaigner;
class ArrayOfTestContact
{

    /**
     * @var TestContact[] $TestContact
     */
    protected $TestContact = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return TestContact[]
     */
    public function getTestContact()
    {
      return $this->TestContact;
    }

    /**
     * @param TestContact[] $TestContact
     * @return ArrayOfTestContact
     */
    public function setTestContact(array $TestContact)
    {
      $this->TestContact = $TestContact;
      return $this;
    }

}
