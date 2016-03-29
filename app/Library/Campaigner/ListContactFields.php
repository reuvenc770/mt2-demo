<?php
namespace App\Library\Campaigner;
class ListContactFields
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var filter $filter
     */
    protected $filter = null;

    /**
     * @param Authentication $authentication
     * @param filter $filter
     */
    public function __construct($authentication, $filter)
    {
      $this->authentication = $authentication;
      $this->filter = $filter;
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
     * @return ListContactFields
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return filter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param filter $filter
     * @return ListContactFields
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

}
