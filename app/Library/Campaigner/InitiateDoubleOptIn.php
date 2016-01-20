<?php
namespace App\Library\Campaigner;
class InitiateDoubleOptIn
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ContactFilter $contactFilter
     */
    protected $contactFilter = null;

    /**
     * @var int $formId
     */
    protected $formId = null;

    /**
     * @param Authentication $authentication
     * @param ContactFilter $contactFilter
     * @param int $formId
     */
    public function __construct($authentication, $contactFilter, $formId)
    {
      $this->authentication = $authentication;
      $this->contactFilter = $contactFilter;
      $this->formId = $formId;
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
     * @return InitiateDoubleOptIn
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ContactFilter
     */
    public function getContactFilter()
    {
      return $this->contactFilter;
    }

    /**
     * @param ContactFilter $contactFilter
     * @return InitiateDoubleOptIn
     */
    public function setContactFilter($contactFilter)
    {
      $this->contactFilter = $contactFilter;
      return $this;
    }

    /**
     * @return int
     */
    public function getFormId()
    {
      return $this->formId;
    }

    /**
     * @param int $formId
     * @return InitiateDoubleOptIn
     */
    public function setFormId($formId)
    {
      $this->formId = $formId;
      return $this;
    }

}
