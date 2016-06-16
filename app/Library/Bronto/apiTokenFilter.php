<?php
namespace App\Library\Bronto;
class apiTokenFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var string[] $id
     */
    protected $id = null;

    /**
     * @var string[] $accountId
     */
    protected $accountId = null;

    /**
     * @var stringValue[] $name
     */
    protected $name = null;

    /**
     * @param filterType $type
     */
    public function __construct($type)
    {
      $this->type = $type;
    }

    /**
     * @return filterType
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param filterType $type
     * @return apiTokenFilter
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string[] $id
     * @return apiTokenFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getAccountId()
    {
      return $this->accountId;
    }

    /**
     * @param string[] $accountId
     * @return apiTokenFilter
     */
    public function setAccountId(array $accountId)
    {
      $this->accountId = $accountId;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param stringValue[] $name
     * @return apiTokenFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

}
