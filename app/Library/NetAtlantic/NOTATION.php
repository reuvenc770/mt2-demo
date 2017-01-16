<?php

namespace App\Library\NetAtlantic;

class NOTATION extends QName
{

    /**
     * @var QName $_
     */
    protected $_ = null;

    /**
     * @var ID $id
     */
    protected $id = null;

    /**
     * @var anyURI $href
     */
    protected $href = null;

    /**
     * @param QName $_
     * @param ID $id
     * @param anyURI $href
     * @param QName $_
     * @param ID $id
     * @param anyURI $href
     */
    public function __construct($_, $id, $href)
    {
      parent::__construct($_, $id, $href);
      $this->_ = $_;
      $this->id = $id;
      $this->href = $href;
    }

    /**
     * @return QName
     */
    public function get_()
    {
      return $this->_;
    }

    /**
     * @param QName $_
     * @return \App\Library\NetAtlantic\NOTATION
     */
    public function set_($_)
    {
      $this->_ = $_;
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
     * @return \App\Library\NetAtlantic\NOTATION
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
     * @return \App\Library\NetAtlantic\NOTATION
     */
    public function setHref($href)
    {
      $this->href = $href;
      return $this;
    }

}
