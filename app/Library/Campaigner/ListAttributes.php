<?php
namespace App\Library\Campaigner;
class ListAttributes
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ListAttributesFilter $filter
     */
    protected $filter = null;

    /**
     * @param Authentication $authentication
     * @param ListAttributesFilter $filter
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
     * @return ListAttributes
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ListAttributesFilter
     */
    public function getFilter()
    {
      return $this->filter;
    }

    /**
     * @param ListAttributesFilter $filter
     * @return ListAttributes
     */
    public function setFilter($filter)
    {
      $this->filter = $filter;
      return $this;
    }

}
