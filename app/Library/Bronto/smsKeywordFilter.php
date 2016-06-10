<?php
namespace App\Library\Bronto;
class smsKeywordFilter
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
     * @var stringValue[] $name
     */
    protected $name = null;

    /**
     * @var string $keywordType
     */
    protected $keywordType = null;

    /**
     * @param filterType $type
     * @param string $keywordType
     */
    public function __construct($type, $keywordType)
    {
      $this->type = $type;
      $this->keywordType = $keywordType;
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
     * @return smsKeywordFilter
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
     * @return smsKeywordFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
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
     * @return smsKeywordFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getKeywordType()
    {
      return $this->keywordType;
    }

    /**
     * @param string $keywordType
     * @return smsKeywordFilter
     */
    public function setKeywordType($keywordType)
    {
      $this->keywordType = $keywordType;
      return $this;
    }

}
