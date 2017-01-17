<?php

namespace App\Library\NetAtlantic;

class MemberBanStruct
{

    /**
     * @var string $Domain
     */
    protected $Domain = null;

    /**
     * @var string $UserName
     */
    protected $UserName = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var string $SiteName
     */
    protected $SiteName = null;

    /**
     * @var BanLogicEnum $BanLogic
     */
    protected $BanLogic = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getDomain()
    {
      return $this->Domain;
    }

    /**
     * @param string $Domain
     * @return \App\Library\NetAtlantic\MemberBanStruct
     */
    public function setDomain($Domain)
    {
      $this->Domain = $Domain;
      return $this;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
      return $this->UserName;
    }

    /**
     * @param string $UserName
     * @return \App\Library\NetAtlantic\MemberBanStruct
     */
    public function setUserName($UserName)
    {
      $this->UserName = $UserName;
      return $this;
    }

    /**
     * @return string
     */
    public function getListName()
    {
      return $this->ListName;
    }

    /**
     * @param string $ListName
     * @return \App\Library\NetAtlantic\MemberBanStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
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
     * @return \App\Library\NetAtlantic\MemberBanStruct
     */
    public function setSiteName($SiteName)
    {
      $this->SiteName = $SiteName;
      return $this;
    }

    /**
     * @return BanLogicEnum
     */
    public function getBanLogic()
    {
      return $this->BanLogic;
    }

    /**
     * @param BanLogicEnum $BanLogic
     * @return \App\Library\NetAtlantic\MemberBanStruct
     */
    public function setBanLogic($BanLogic)
    {
      $this->BanLogic = $BanLogic;
      return $this;
    }

}
