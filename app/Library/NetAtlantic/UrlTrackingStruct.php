<?php

namespace App\Library\NetAtlantic;

class UrlTrackingStruct
{

    /**
     * @var string $UniqueOpens
     */
    protected $UniqueOpens = null;

    /**
     * @var string $Opens
     */
    protected $Opens = null;

    /**
     * @var string $Url
     */
    protected $Url = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getUniqueOpens()
    {
      return $this->UniqueOpens;
    }

    /**
     * @param string $UniqueOpens
     * @return \App\Library\NetAtlantic\UrlTrackingStruct
     */
    public function setUniqueOpens($UniqueOpens)
    {
      $this->UniqueOpens = $UniqueOpens;
      return $this;
    }

    /**
     * @return string
     */
    public function getOpens()
    {
      return $this->Opens;
    }

    /**
     * @param string $Opens
     * @return \App\Library\NetAtlantic\UrlTrackingStruct
     */
    public function setOpens($Opens)
    {
      $this->Opens = $Opens;
      return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
      return $this->Url;
    }

    /**
     * @param string $Url
     * @return \App\Library\NetAtlantic\UrlTrackingStruct
     */
    public function setUrl($Url)
    {
      $this->Url = $Url;
      return $this;
    }

}
