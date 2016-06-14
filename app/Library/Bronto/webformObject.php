<?php
namespace App\Library\Bronto;
class webformObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var boolean $isDefault
     */
    protected $isDefault = null;

    /**
     * @var \DateTime $modified
     */
    protected $modified = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $type
     * @param boolean $isDefault
     * @param \DateTime $modified
     */
    public function __construct($id, $name, $type, $isDefault, \DateTime $modified)
    {
      $this->id = $id;
      $this->name = $name;
      $this->type = $type;
      $this->isDefault = $isDefault;
      $this->modified = $modified->format(\DateTime::ATOM);
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return webformObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return webformObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return webformObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDefault()
    {
      return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     * @return webformObject
     */
    public function setIsDefault($isDefault)
    {
      $this->isDefault = $isDefault;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
      if ($this->modified == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->modified);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $modified
     * @return webformObject
     */
    public function setModified(\DateTime $modified)
    {
      $this->modified = $modified->format(\DateTime::ATOM);
      return $this;
    }

}
