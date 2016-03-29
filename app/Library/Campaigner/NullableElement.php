<?php
namespace App\Library\Campaigner;
class NullableElement
{

    /**
     * @var string $_
     */
    protected $_ = null;

    /**
     * @var boolean $IsNull
     */
    protected $IsNull = null;

    /**
     * @param string $_
     * @param boolean $IsNull
     */
    public function __construct($_, $IsNull)
    {
      $this->_ = $_;
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
     * @return NullableElement
     */
    public function set_($_)
    {
      $this->_ = $_;
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
     * @return NullableElement
     */
    public function setIsNull($IsNull)
    {
      $this->IsNull = $IsNull;
      return $this;
    }

}
