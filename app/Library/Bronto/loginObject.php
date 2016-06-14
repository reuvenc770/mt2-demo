<?php

class loginObject
{

    /**
     * @var string $username
     */
    protected $username = null;

    /**
     * @var string $password
     */
    protected $password = null;

    /**
     * @var contactInformation $contactInformation
     */
    protected $contactInformation = null;

    /**
     * @var boolean $permissionAgencyAdmin
     */
    protected $permissionAgencyAdmin = null;

    /**
     * @var boolean $permissionAdmin
     */
    protected $permissionAdmin = null;

    /**
     * @var boolean $permissionApi
     */
    protected $permissionApi = null;

    /**
     * @var boolean $permissionUpgrade
     */
    protected $permissionUpgrade = null;

    /**
     * @var boolean $permissionFatigueOverride
     */
    protected $permissionFatigueOverride = null;

    /**
     * @var boolean $permissionMessageCompose
     */
    protected $permissionMessageCompose = null;

    /**
     * @var boolean $permissionMessageApprove
     */
    protected $permissionMessageApprove = null;

    /**
     * @var boolean $permissionMessageDelete
     */
    protected $permissionMessageDelete = null;

    /**
     * @var boolean $permissionAutomatorCompose
     */
    protected $permissionAutomatorCompose = null;

    /**
     * @var boolean $permissionListCreateSend
     */
    protected $permissionListCreateSend = null;

    /**
     * @var boolean $permissionListCreate
     */
    protected $permissionListCreate = null;

    /**
     * @var boolean $permissionSegmentCreate
     */
    protected $permissionSegmentCreate = null;

    /**
     * @var boolean $permissionFieldCreate
     */
    protected $permissionFieldCreate = null;

    /**
     * @var boolean $permissionFieldReorder
     */
    protected $permissionFieldReorder = null;

    /**
     * @var boolean $permissionSubscriberCreate
     */
    protected $permissionSubscriberCreate = null;

    /**
     * @var boolean $permissionSubscriberView
     */
    protected $permissionSubscriberView = null;

    /**
     * @param string $username
     * @param string $password
     * @param contactInformation $contactInformation
     * @param boolean $permissionAgencyAdmin
     * @param boolean $permissionAdmin
     * @param boolean $permissionApi
     * @param boolean $permissionUpgrade
     * @param boolean $permissionFatigueOverride
     * @param boolean $permissionMessageCompose
     * @param boolean $permissionMessageApprove
     * @param boolean $permissionMessageDelete
     * @param boolean $permissionAutomatorCompose
     * @param boolean $permissionListCreateSend
     * @param boolean $permissionListCreate
     * @param boolean $permissionSegmentCreate
     * @param boolean $permissionFieldCreate
     * @param boolean $permissionFieldReorder
     * @param boolean $permissionSubscriberCreate
     * @param boolean $permissionSubscriberView
     */
    public function __construct($username, $password, $contactInformation, $permissionAgencyAdmin, $permissionAdmin, $permissionApi, $permissionUpgrade, $permissionFatigueOverride, $permissionMessageCompose, $permissionMessageApprove, $permissionMessageDelete, $permissionAutomatorCompose, $permissionListCreateSend, $permissionListCreate, $permissionSegmentCreate, $permissionFieldCreate, $permissionFieldReorder, $permissionSubscriberCreate, $permissionSubscriberView)
    {
      $this->username = $username;
      $this->password = $password;
      $this->contactInformation = $contactInformation;
      $this->permissionAgencyAdmin = $permissionAgencyAdmin;
      $this->permissionAdmin = $permissionAdmin;
      $this->permissionApi = $permissionApi;
      $this->permissionUpgrade = $permissionUpgrade;
      $this->permissionFatigueOverride = $permissionFatigueOverride;
      $this->permissionMessageCompose = $permissionMessageCompose;
      $this->permissionMessageApprove = $permissionMessageApprove;
      $this->permissionMessageDelete = $permissionMessageDelete;
      $this->permissionAutomatorCompose = $permissionAutomatorCompose;
      $this->permissionListCreateSend = $permissionListCreateSend;
      $this->permissionListCreate = $permissionListCreate;
      $this->permissionSegmentCreate = $permissionSegmentCreate;
      $this->permissionFieldCreate = $permissionFieldCreate;
      $this->permissionFieldReorder = $permissionFieldReorder;
      $this->permissionSubscriberCreate = $permissionSubscriberCreate;
      $this->permissionSubscriberView = $permissionSubscriberView;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
      return $this->username;
    }

    /**
     * @param string $username
     * @return loginObject
     */
    public function setUsername($username)
    {
      $this->username = $username;
      return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
      return $this->password;
    }

    /**
     * @param string $password
     * @return loginObject
     */
    public function setPassword($password)
    {
      $this->password = $password;
      return $this;
    }

    /**
     * @return contactInformation
     */
    public function getContactInformation()
    {
      return $this->contactInformation;
    }

    /**
     * @param contactInformation $contactInformation
     * @return loginObject
     */
    public function setContactInformation($contactInformation)
    {
      $this->contactInformation = $contactInformation;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionAgencyAdmin()
    {
      return $this->permissionAgencyAdmin;
    }

    /**
     * @param boolean $permissionAgencyAdmin
     * @return loginObject
     */
    public function setPermissionAgencyAdmin($permissionAgencyAdmin)
    {
      $this->permissionAgencyAdmin = $permissionAgencyAdmin;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionAdmin()
    {
      return $this->permissionAdmin;
    }

    /**
     * @param boolean $permissionAdmin
     * @return loginObject
     */
    public function setPermissionAdmin($permissionAdmin)
    {
      $this->permissionAdmin = $permissionAdmin;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionApi()
    {
      return $this->permissionApi;
    }

    /**
     * @param boolean $permissionApi
     * @return loginObject
     */
    public function setPermissionApi($permissionApi)
    {
      $this->permissionApi = $permissionApi;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionUpgrade()
    {
      return $this->permissionUpgrade;
    }

    /**
     * @param boolean $permissionUpgrade
     * @return loginObject
     */
    public function setPermissionUpgrade($permissionUpgrade)
    {
      $this->permissionUpgrade = $permissionUpgrade;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionFatigueOverride()
    {
      return $this->permissionFatigueOverride;
    }

    /**
     * @param boolean $permissionFatigueOverride
     * @return loginObject
     */
    public function setPermissionFatigueOverride($permissionFatigueOverride)
    {
      $this->permissionFatigueOverride = $permissionFatigueOverride;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionMessageCompose()
    {
      return $this->permissionMessageCompose;
    }

    /**
     * @param boolean $permissionMessageCompose
     * @return loginObject
     */
    public function setPermissionMessageCompose($permissionMessageCompose)
    {
      $this->permissionMessageCompose = $permissionMessageCompose;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionMessageApprove()
    {
      return $this->permissionMessageApprove;
    }

    /**
     * @param boolean $permissionMessageApprove
     * @return loginObject
     */
    public function setPermissionMessageApprove($permissionMessageApprove)
    {
      $this->permissionMessageApprove = $permissionMessageApprove;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionMessageDelete()
    {
      return $this->permissionMessageDelete;
    }

    /**
     * @param boolean $permissionMessageDelete
     * @return loginObject
     */
    public function setPermissionMessageDelete($permissionMessageDelete)
    {
      $this->permissionMessageDelete = $permissionMessageDelete;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionAutomatorCompose()
    {
      return $this->permissionAutomatorCompose;
    }

    /**
     * @param boolean $permissionAutomatorCompose
     * @return loginObject
     */
    public function setPermissionAutomatorCompose($permissionAutomatorCompose)
    {
      $this->permissionAutomatorCompose = $permissionAutomatorCompose;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionListCreateSend()
    {
      return $this->permissionListCreateSend;
    }

    /**
     * @param boolean $permissionListCreateSend
     * @return loginObject
     */
    public function setPermissionListCreateSend($permissionListCreateSend)
    {
      $this->permissionListCreateSend = $permissionListCreateSend;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionListCreate()
    {
      return $this->permissionListCreate;
    }

    /**
     * @param boolean $permissionListCreate
     * @return loginObject
     */
    public function setPermissionListCreate($permissionListCreate)
    {
      $this->permissionListCreate = $permissionListCreate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionSegmentCreate()
    {
      return $this->permissionSegmentCreate;
    }

    /**
     * @param boolean $permissionSegmentCreate
     * @return loginObject
     */
    public function setPermissionSegmentCreate($permissionSegmentCreate)
    {
      $this->permissionSegmentCreate = $permissionSegmentCreate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionFieldCreate()
    {
      return $this->permissionFieldCreate;
    }

    /**
     * @param boolean $permissionFieldCreate
     * @return loginObject
     */
    public function setPermissionFieldCreate($permissionFieldCreate)
    {
      $this->permissionFieldCreate = $permissionFieldCreate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionFieldReorder()
    {
      return $this->permissionFieldReorder;
    }

    /**
     * @param boolean $permissionFieldReorder
     * @return loginObject
     */
    public function setPermissionFieldReorder($permissionFieldReorder)
    {
      $this->permissionFieldReorder = $permissionFieldReorder;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionSubscriberCreate()
    {
      return $this->permissionSubscriberCreate;
    }

    /**
     * @param boolean $permissionSubscriberCreate
     * @return loginObject
     */
    public function setPermissionSubscriberCreate($permissionSubscriberCreate)
    {
      $this->permissionSubscriberCreate = $permissionSubscriberCreate;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getPermissionSubscriberView()
    {
      return $this->permissionSubscriberView;
    }

    /**
     * @param boolean $permissionSubscriberView
     * @return loginObject
     */
    public function setPermissionSubscriberView($permissionSubscriberView)
    {
      $this->permissionSubscriberView = $permissionSubscriberView;
      return $this;
    }

}
