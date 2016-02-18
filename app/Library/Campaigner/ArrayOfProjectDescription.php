<?php
namespace App\Library\Campaigner;
class ArrayOfProjectDescription
{

    /**
     * @var ProjectDescription[] $ProjectDescription
     */
    protected $ProjectDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ProjectDescription[]
     */
    public function getProjectDescription()
    {
      return $this->ProjectDescription;
    }

    /**
     * @param ProjectDescription[] $ProjectDescription
     * @return ArrayOfProjectDescription
     */
    public function setProjectDescription(array $ProjectDescription)
    {
      $this->ProjectDescription = $ProjectDescription;
      return $this;
    }

}
