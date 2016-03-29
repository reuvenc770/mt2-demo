<?php
namespace App\Library\Campaigner;
class DeleteAttribute
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $id
     */
    protected $id = null;

    /**
     * @param Authentication $authentication
     * @param int $id
     */
    public function __construct($authentication, $id)
    {
      $this->authentication = $authentication;
      $this->id = $id;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return DeleteAttribute
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param int $id
     * @return DeleteAttribute
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

}
