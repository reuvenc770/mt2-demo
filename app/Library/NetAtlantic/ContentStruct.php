<?php

namespace App\Library\NetAtlantic;

class ContentStruct
{

    /**
     * @var string $HeaderTo
     */
    protected $HeaderTo = null;

    /**
     * @var boolean $IsTemplate
     */
    protected $IsTemplate = null;

    /**
     * @var DocTypeEnum $DocType
     */
    protected $DocType = null;

    /**
     * @var int $ContentID
     */
    protected $ContentID = null;

    /**
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var string $NativeTitle
     */
    protected $NativeTitle = null;

    /**
     * @var string $HeaderFrom
     */
    protected $HeaderFrom = null;

    /**
     * @var string $Title
     */
    protected $Title = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var string $SiteName
     */
    protected $SiteName = null;

    /**
     * @var boolean $IsReadOnly
     */
    protected $IsReadOnly = null;

    /**
     * @var \DateTime $DateCreated
     */
    protected $DateCreated = null;

    /**
     * @var ArrayOfDocPart $DocParts
     */
    protected $DocParts = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getHeaderTo()
    {
      return $this->HeaderTo;
    }

    /**
     * @param string $HeaderTo
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setHeaderTo($HeaderTo)
    {
      $this->HeaderTo = $HeaderTo;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsTemplate()
    {
      return $this->IsTemplate;
    }

    /**
     * @param boolean $IsTemplate
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setIsTemplate($IsTemplate)
    {
      $this->IsTemplate = $IsTemplate;
      return $this;
    }

    /**
     * @return DocTypeEnum
     */
    public function getDocType()
    {
      return $this->DocType;
    }

    /**
     * @param DocTypeEnum $DocType
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setDocType($DocType)
    {
      $this->DocType = $DocType;
      return $this;
    }

    /**
     * @return int
     */
    public function getContentID()
    {
      return $this->ContentID;
    }

    /**
     * @param int $ContentID
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setContentID($ContentID)
    {
      $this->ContentID = $ContentID;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->Description;
    }

    /**
     * @param string $Description
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return string
     */
    public function getNativeTitle()
    {
      return $this->NativeTitle;
    }

    /**
     * @param string $NativeTitle
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setNativeTitle($NativeTitle)
    {
      $this->NativeTitle = $NativeTitle;
      return $this;
    }

    /**
     * @return string
     */
    public function getHeaderFrom()
    {
      return $this->HeaderFrom;
    }

    /**
     * @param string $HeaderFrom
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setHeaderFrom($HeaderFrom)
    {
      $this->HeaderFrom = $HeaderFrom;
      return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
      return $this->Title;
    }

    /**
     * @param string $Title
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setTitle($Title)
    {
      $this->Title = $Title;
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
     * @return \App\Library\NetAtlantic\ContentStruct
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
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setSiteName($SiteName)
    {
      $this->SiteName = $SiteName;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsReadOnly()
    {
      return $this->IsReadOnly;
    }

    /**
     * @param boolean $IsReadOnly
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setIsReadOnly($IsReadOnly)
    {
      $this->IsReadOnly = $IsReadOnly;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
      if ($this->DateCreated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->DateCreated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $DateCreated
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setDateCreated(\DateTime $DateCreated = null)
    {
      if ($DateCreated == null) {
       $this->DateCreated = null;
      } else {
        $this->DateCreated = $DateCreated->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return ArrayOfDocPart
     */
    public function getDocParts()
    {
      return $this->DocParts;
    }

    /**
     * @param ArrayOfDocPart $DocParts
     * @return \App\Library\NetAtlantic\ContentStruct
     */
    public function setDocParts($DocParts)
    {
      $this->DocParts = $DocParts;
      return $this;
    }

}
