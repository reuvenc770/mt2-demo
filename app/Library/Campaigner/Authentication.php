<?php
namespace App\Library\Campaigner;
class Authentication
{

    /**
     * @var string $Username
     */
    protected $Username = null;

    /**
     * @var string $Password
     */
    protected $Password = null;

    /**
     * @param string $Username
     * @param string $Password
     */
    public function __construct($Username, $Password)
    {
      $this->Username = $Username;
      $this->Password = $Password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
      return $this->Username;
    }

    /**
     * @param string $Username
     * @return Authentication
     */
    public function setUsername($Username)
    {
      $this->Username = $Username;
      return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
      return $this->Password;
    }

    /**
     * @param string $Password
     * @return Authentication
     */
    public function setPassword($Password)
    {
      $this->Password = $Password;
      return $this;
    }

}
