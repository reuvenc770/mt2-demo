<?php

namespace App\Library\NetAtlantic;

class SegmentStruct
{

    /**
     * @var int $SegmentID
     */
    protected $SegmentID = null;

    /**
     * @var string $SegmentName
     */
    protected $SegmentName = null;

    /**
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var SegmentTypeEnum $SegmentType
     */
    protected $SegmentType = null;

    /**
     * @var string $ListName
     */
    protected $ListName = null;

    /**
     * @var int $NumTestRecords
     */
    protected $NumTestRecords = null;

    /**
     * @var string $ClauseAdd
     */
    protected $ClauseAdd = null;

    /**
     * @var string $ClauseWhere
     */
    protected $ClauseWhere = null;

    /**
     * @var string $ClauseAfterSelect
     */
    protected $ClauseAfterSelect = null;

    /**
     * @var string $ClauseFrom
     */
    protected $ClauseFrom = null;

    /**
     * @var string $ClauseOrderBy
     */
    protected $ClauseOrderBy = null;

    /**
     * @var string $ClauseSelect
     */
    protected $ClauseSelect = null;

    /**
     * @var boolean $AddWhereList
     */
    protected $AddWhereList = null;

    /**
     * @var boolean $AddWhereMemberType
     */
    protected $AddWhereMemberType = null;

    /**
     * @var boolean $AddWhereSubType
     */
    protected $AddWhereSubType = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getSegmentID()
    {
      return $this->SegmentID;
    }

    /**
     * @param int $SegmentID
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setSegmentID($SegmentID)
    {
      $this->SegmentID = $SegmentID;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegmentName()
    {
      return $this->SegmentName;
    }

    /**
     * @param string $SegmentName
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setSegmentName($SegmentName)
    {
      $this->SegmentName = $SegmentName;
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
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return SegmentTypeEnum
     */
    public function getSegmentType()
    {
      return $this->SegmentType;
    }

    /**
     * @param SegmentTypeEnum $SegmentType
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setSegmentType($SegmentType)
    {
      $this->SegmentType = $SegmentType;
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
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setListName($ListName)
    {
      $this->ListName = $ListName;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumTestRecords()
    {
      return $this->NumTestRecords;
    }

    /**
     * @param int $NumTestRecords
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setNumTestRecords($NumTestRecords)
    {
      $this->NumTestRecords = $NumTestRecords;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseAdd()
    {
      return $this->ClauseAdd;
    }

    /**
     * @param string $ClauseAdd
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseAdd($ClauseAdd)
    {
      $this->ClauseAdd = $ClauseAdd;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseWhere()
    {
      return $this->ClauseWhere;
    }

    /**
     * @param string $ClauseWhere
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseWhere($ClauseWhere)
    {
      $this->ClauseWhere = $ClauseWhere;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseAfterSelect()
    {
      return $this->ClauseAfterSelect;
    }

    /**
     * @param string $ClauseAfterSelect
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseAfterSelect($ClauseAfterSelect)
    {
      $this->ClauseAfterSelect = $ClauseAfterSelect;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseFrom()
    {
      return $this->ClauseFrom;
    }

    /**
     * @param string $ClauseFrom
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseFrom($ClauseFrom)
    {
      $this->ClauseFrom = $ClauseFrom;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseOrderBy()
    {
      return $this->ClauseOrderBy;
    }

    /**
     * @param string $ClauseOrderBy
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseOrderBy($ClauseOrderBy)
    {
      $this->ClauseOrderBy = $ClauseOrderBy;
      return $this;
    }

    /**
     * @return string
     */
    public function getClauseSelect()
    {
      return $this->ClauseSelect;
    }

    /**
     * @param string $ClauseSelect
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setClauseSelect($ClauseSelect)
    {
      $this->ClauseSelect = $ClauseSelect;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAddWhereList()
    {
      return $this->AddWhereList;
    }

    /**
     * @param boolean $AddWhereList
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setAddWhereList($AddWhereList)
    {
      $this->AddWhereList = $AddWhereList;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAddWhereMemberType()
    {
      return $this->AddWhereMemberType;
    }

    /**
     * @param boolean $AddWhereMemberType
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setAddWhereMemberType($AddWhereMemberType)
    {
      $this->AddWhereMemberType = $AddWhereMemberType;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAddWhereSubType()
    {
      return $this->AddWhereSubType;
    }

    /**
     * @param boolean $AddWhereSubType
     * @return \App\Library\NetAtlantic\SegmentStruct
     */
    public function setAddWhereSubType($AddWhereSubType)
    {
      $this->AddWhereSubType = $AddWhereSubType;
      return $this;
    }

}
