<?php

namespace App\Library\NetAtlantic;

class ArrayCustom
{

    /**
     * @var string $any
     */
    protected $any = null;

    /**
     * @var string $arrayType
     */
    protected $arrayType = null;

    /**
     * @var arrayCoordinate $offset
     */
    protected $offset = null;

    /**
     * @var ID $id
     */
    protected $id = null;

    /**
     * @var anyURI $href
     */
    protected $href = null;

    /**
     * @param string $any
     * @param string $arrayType
     * @param arrayCoordinate $offset
     * @param ID $id
     * @param anyURI $href
     */
    public function __construct($any, $arrayType, $offset, $id, $href)
    {
      $this->any = $any;
      $this->arrayType = $arrayType;
      $this->offset = $offset;
      $this->id = $id;
      $this->href = $href;
    }

    /**
     * @return string
     */
    public function getAny()
    {
      return $this->any;
    }

    /**
     * @param string $any
     * @return \App\Library\NetAtlantic\Array
     */
    public function setAny($any)
    {
      $this->any = $any;
      return $this;
    }

    /**
     * @return string
     */
    public function getArrayType()
    {
      return $this->arrayType;
    }

    /**
     * @param string $arrayType
     * @return \App\Library\NetAtlantic\Array
     */
    public function setArrayType($arrayType)
    {
      $this->arrayType = $arrayType;
      return $this;
    }

    /**
     * @return arrayCoordinate
     */
    public function getOffset()
    {
      return $this->offset;
    }

    /**
     * @param arrayCoordinate $offset
     * @return \App\Library\NetAtlantic\Array
     */
    public function setOffset($offset)
    {
      $this->offset = $offset;
      return $this;
    }

    /**
     * @return ID
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param ID $id
     * @return \App\Library\NetAtlantic\Array
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return anyURI
     */
    public function getHref()
    {
      return $this->href;
    }

    /**
     * @param anyURI $href
     * @return \App\Library\NetAtlantic\Array
     */
    public function setHref($href)
    {
      $this->href = $href;
      return $this;
    }

}
