<?php
namespace App\Library\Campaigner;
class CustomAttribute
{

    /**
     * @var string $_
     */
    protected $_ = null;

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var boolean $IsNull
     */
    protected $IsNull = null;

    /**
     * @param string $_
     * @param int $Id
     * @param boolean $IsNull
     */
    public function __construct($_, $Id, $IsNull)
    {
      $this->_ = $_;
      $this->Id = $Id;
      $this->IsNull = $IsNull;
    }

    /**
     * @return string
     */
    public function get_()
    {
      return $this->_;
    }

    /**
     * @param string $_
     * @return CustomAttribute
     */
    public function set_($_)
    {
      $this->_ = $_;
      return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return CustomAttribute
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsNull()
    {
      return $this->IsNull;
    }

    /**
     * @param boolean $IsNull
     * @return CustomAttribute
     */
    public function setIsNull($IsNull)
    {
      $this->IsNull = $IsNull;
      return $this;
    }

}
