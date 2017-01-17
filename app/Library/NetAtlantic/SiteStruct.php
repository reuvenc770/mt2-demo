<?php

namespace App\Library\NetAtlantic;

class SiteStruct
{

    /**
     * @var int $SiteID
     */
    protected $SiteID = null;

    /**
     * @var string $SiteName
     */
    protected $SiteName = null;

    /**
     * @var string $SiteDescription
     */
    protected $SiteDescription = null;

    /**
     * @var string $HostName
     */
    protected $HostName = null;

    /**
     * @var string $WebInterfaceURL
     */
    protected $WebInterfaceURL = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getSiteID()
    {
      return $this->SiteID;
    }

    /**
     * @param int $SiteID
     * @return \App\Library\NetAtlantic\SiteStruct
     */
    public function setSiteID($SiteID)
    {
      $this->SiteID = $SiteID;
      return $this;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
      return $this->SiteName;
    }

    /**
     * @param string $SiteName
     * @return \App\Library\NetAtlantic\SiteStruct
     */
    public function setSiteName($SiteName)
    {
      $this->SiteName = $SiteName;
      return $this;
    }

    /**
     * @return string
     */
    public function getSiteDescription()
    {
      return $this->SiteDescription;
    }

    /**
     * @param string $SiteDescription
     * @return \App\Library\NetAtlantic\SiteStruct
     */
    public function setSiteDescription($SiteDescription)
    {
      $this->SiteDescription = $SiteDescription;
      return $this;
    }

    /**
     * @return string
     */
    public function getHostName()
    {
      return $this->HostName;
    }

    /**
     * @param string $HostName
     * @return \App\Library\NetAtlantic\SiteStruct
     */
    public function setHostName($HostName)
    {
      $this->HostName = $HostName;
      return $this;
    }

    /**
     * @return string
     */
    public function getWebInterfaceURL()
    {
      return $this->WebInterfaceURL;
    }

    /**
     * @param string $WebInterfaceURL
     * @return \App\Library\NetAtlantic\SiteStruct
     */
    public function setWebInterfaceURL($WebInterfaceURL)
    {
      $this->WebInterfaceURL = $WebInterfaceURL;
      return $this;
    }

}
