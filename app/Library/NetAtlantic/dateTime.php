<?php

namespace App\Library\NetAtlantic;

class dateTime
{

    /**
     * @var \DateTime $_
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
     * @param \DateTime $_
     * @param ID $id
     * @param anyURI $href
     */
    public function __construct(\DateTime $_, $id, $href)
    {
      $this->_ = $_->format(\DateTime::ATOM);
      $this->id = $id;
      $this->href = $href;
    }

    /**
     * @return \DateTime
     */
    public function get_()
    {
      if ($this->_ == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->_);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $_
     * @return \App\Library\NetAtlantic\dateTime
     */
    public function set_(\DateTime $_)
    {
      $this->_ = $_->format(\DateTime::ATOM);
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
     * @return \App\Library\NetAtlantic\dateTime
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
     * @return \App\Library\NetAtlantic\dateTime
     */
    public function setHref($href)
    {
      $this->href = $href;
      return $this;
    }

}
