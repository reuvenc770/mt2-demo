<?php
namespace App\Library\Bronto;
class apiTokenObject
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
     * @var int $permissions
     */
    protected $permissions = null;

    /**
     * @var boolean $active
     */
    protected $active = null;

    /**
     * @var \DateTime $created
     */
    protected $created = null;

    /**
     * @var \DateTime $modified
     */
    protected $modified = null;

    /**
     * @var string $accountId
     */
    protected $accountId = null;

    /**
     * @param string $id
     * @param string $name
     * @param int $permissions
     * @param boolean $active
     * @param \DateTime $created
     * @param \DateTime $modified
     * @param string $accountId
     */
    public function __construct($id, $name, $permissions, $active, \DateTime $created, \DateTime $modified, $accountId)
    {
      $this->id = $id;
      $this->name = $name;
      $this->permissions = $permissions;
      $this->active = $active;
      $this->created = $created->format(\DateTime::ATOM);
      $this->modified = $modified->format(\DateTime::ATOM);
      $this->accountId = $accountId;
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
     * @return apiTokenObject
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
     * @return apiTokenObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return int
     */
    public function getPermissions()
    {
      return $this->permissions;
    }

    /**
     * @param int $permissions
     * @return apiTokenObject
     */
    public function setPermissions($permissions)
    {
      $this->permissions = $permissions;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param boolean $active
     * @return apiTokenObject
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
      if ($this->created == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->created);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $created
     * @return apiTokenObject
     */
    public function setCreated(\DateTime $created)
    {
      $this->created = $created->format(\DateTime::ATOM);
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
     * @return apiTokenObject
     */
    public function setModified(\DateTime $modified)
    {
      $this->modified = $modified->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
      return $this->accountId;
    }

    /**
     * @param string $accountId
     * @return apiTokenObject
     */
    public function setAccountId($accountId)
    {
      $this->accountId = $accountId;
      return $this;
    }

}
