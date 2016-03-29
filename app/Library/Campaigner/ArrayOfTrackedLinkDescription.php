<?php
namespace App\Library\Campaigner;
class ArrayOfTrackedLinkDescription
{

    /**
     * @var TrackedLinkDescription[] $TrackedLinkDescription
     */
    protected $TrackedLinkDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return TrackedLinkDescription[]
     */
    public function getTrackedLinkDescription()
    {
      return $this->TrackedLinkDescription;
    }

    /**
     * @param TrackedLinkDescription[] $TrackedLinkDescription
     * @return ArrayOfTrackedLinkDescription
     */
    public function setTrackedLinkDescription(array $TrackedLinkDescription)
    {
      $this->TrackedLinkDescription = $TrackedLinkDescription;
      return $this;
    }

}
