<?php
namespace App\Library\Bronto;
class messageRuleObject
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
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $type
     * @param string $messageId
     */
    public function __construct($id, $name, $type, $messageId)
    {
      $this->id = $id;
      $this->name = $name;
      $this->type = $type;
      $this->messageId = $messageId;
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
     * @return messageRuleObject
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
     * @return messageRuleObject
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
     * @return messageRuleObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
      return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return messageRuleObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

}
