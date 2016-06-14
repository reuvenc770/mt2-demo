<?php

class generalSettings
{

    /**
     * @var string $emergencyEmail
     */
    protected $emergencyEmail = null;

    /**
     * @var int $dailyFrequencyCap
     */
    protected $dailyFrequencyCap = null;

    /**
     * @var int $weeklyFrequencyCap
     */
    protected $weeklyFrequencyCap = null;

    /**
     * @var int $monthlyFrequencyCap
     */
    protected $monthlyFrequencyCap = null;

    /**
     * @var boolean $textDelivery
     */
    protected $textDelivery = null;

    /**
     * @var boolean $textPreference
     */
    protected $textPreference = null;

    /**
     * @var boolean $useSSL
     */
    protected $useSSL = null;

    /**
     * @var boolean $sendUsageAlerts
     */
    protected $sendUsageAlerts = null;

    /**
     * @var string $usageAlertEmail
     */
    protected $usageAlertEmail = null;

    /**
     * @var int $currentContacts
     */
    protected $currentContacts = null;

    /**
     * @var int $maxContacts
     */
    protected $maxContacts = null;

    /**
     * @var int $currentMonthlyEmails
     */
    protected $currentMonthlyEmails = null;

    /**
     * @var int $currentHostingSize
     */
    protected $currentHostingSize = null;

    /**
     * @var int $maxHostingSize
     */
    protected $maxHostingSize = null;

    /**
     * @var boolean $agencyTemplateuploadPerm
     */
    protected $agencyTemplateuploadPerm = null;

    /**
     * @var boolean $defaultTemplates
     */
    protected $defaultTemplates = null;

    /**
     * @var boolean $enableInboxPreviews
     */
    protected $enableInboxPreviews = null;

    /**
     * @var boolean $allowCustomizedBranding
     */
    protected $allowCustomizedBranding = null;

    /**
     * @var int $bounceLimit
     */
    protected $bounceLimit = null;

    /**
     * @param string $emergencyEmail
     * @param int $dailyFrequencyCap
     * @param int $weeklyFrequencyCap
     * @param int $monthlyFrequencyCap
     * @param boolean $textDelivery
     * @param boolean $textPreference
     * @param boolean $useSSL
     * @param boolean $sendUsageAlerts
     * @param string $usageAlertEmail
     * @param int $currentContacts
     * @param int $maxContacts
     * @param int $currentMonthlyEmails
     * @param int $currentHostingSize
     * @param int $maxHostingSize
     * @param boolean $agencyTemplateuploadPerm
     * @param boolean $defaultTemplates
     * @param boolean $enableInboxPreviews
     * @param boolean $allowCustomizedBranding
     * @param int $bounceLimit
     */
    public function __construct($emergencyEmail, $dailyFrequencyCap, $weeklyFrequencyCap, $monthlyFrequencyCap, $textDelivery, $textPreference, $useSSL, $sendUsageAlerts, $usageAlertEmail, $currentContacts, $maxContacts, $currentMonthlyEmails, $currentHostingSize, $maxHostingSize, $agencyTemplateuploadPerm, $defaultTemplates, $enableInboxPreviews, $allowCustomizedBranding, $bounceLimit)
    {
      $this->emergencyEmail = $emergencyEmail;
      $this->dailyFrequencyCap = $dailyFrequencyCap;
      $this->weeklyFrequencyCap = $weeklyFrequencyCap;
      $this->monthlyFrequencyCap = $monthlyFrequencyCap;
      $this->textDelivery = $textDelivery;
      $this->textPreference = $textPreference;
      $this->useSSL = $useSSL;
      $this->sendUsageAlerts = $sendUsageAlerts;
      $this->usageAlertEmail = $usageAlertEmail;
      $this->currentContacts = $currentContacts;
      $this->maxContacts = $maxContacts;
      $this->currentMonthlyEmails = $currentMonthlyEmails;
      $this->currentHostingSize = $currentHostingSize;
      $this->maxHostingSize = $maxHostingSize;
      $this->agencyTemplateuploadPerm = $agencyTemplateuploadPerm;
      $this->defaultTemplates = $defaultTemplates;
      $this->enableInboxPreviews = $enableInboxPreviews;
      $this->allowCustomizedBranding = $allowCustomizedBranding;
      $this->bounceLimit = $bounceLimit;
    }

    /**
     * @return string
     */
    public function getEmergencyEmail()
    {
      return $this->emergencyEmail;
    }

    /**
     * @param string $emergencyEmail
     * @return generalSettings
     */
    public function setEmergencyEmail($emergencyEmail)
    {
      $this->emergencyEmail = $emergencyEmail;
      return $this;
    }

    /**
     * @return int
     */
    public function getDailyFrequencyCap()
    {
      return $this->dailyFrequencyCap;
    }

    /**
     * @param int $dailyFrequencyCap
     * @return generalSettings
     */
    public function setDailyFrequencyCap($dailyFrequencyCap)
    {
      $this->dailyFrequencyCap = $dailyFrequencyCap;
      return $this;
    }

    /**
     * @return int
     */
    public function getWeeklyFrequencyCap()
    {
      return $this->weeklyFrequencyCap;
    }

    /**
     * @param int $weeklyFrequencyCap
     * @return generalSettings
     */
    public function setWeeklyFrequencyCap($weeklyFrequencyCap)
    {
      $this->weeklyFrequencyCap = $weeklyFrequencyCap;
      return $this;
    }

    /**
     * @return int
     */
    public function getMonthlyFrequencyCap()
    {
      return $this->monthlyFrequencyCap;
    }

    /**
     * @param int $monthlyFrequencyCap
     * @return generalSettings
     */
    public function setMonthlyFrequencyCap($monthlyFrequencyCap)
    {
      $this->monthlyFrequencyCap = $monthlyFrequencyCap;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTextDelivery()
    {
      return $this->textDelivery;
    }

    /**
     * @param boolean $textDelivery
     * @return generalSettings
     */
    public function setTextDelivery($textDelivery)
    {
      $this->textDelivery = $textDelivery;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTextPreference()
    {
      return $this->textPreference;
    }

    /**
     * @param boolean $textPreference
     * @return generalSettings
     */
    public function setTextPreference($textPreference)
    {
      $this->textPreference = $textPreference;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getUseSSL()
    {
      return $this->useSSL;
    }

    /**
     * @param boolean $useSSL
     * @return generalSettings
     */
    public function setUseSSL($useSSL)
    {
      $this->useSSL = $useSSL;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSendUsageAlerts()
    {
      return $this->sendUsageAlerts;
    }

    /**
     * @param boolean $sendUsageAlerts
     * @return generalSettings
     */
    public function setSendUsageAlerts($sendUsageAlerts)
    {
      $this->sendUsageAlerts = $sendUsageAlerts;
      return $this;
    }

    /**
     * @return string
     */
    public function getUsageAlertEmail()
    {
      return $this->usageAlertEmail;
    }

    /**
     * @param string $usageAlertEmail
     * @return generalSettings
     */
    public function setUsageAlertEmail($usageAlertEmail)
    {
      $this->usageAlertEmail = $usageAlertEmail;
      return $this;
    }

    /**
     * @return int
     */
    public function getCurrentContacts()
    {
      return $this->currentContacts;
    }

    /**
     * @param int $currentContacts
     * @return generalSettings
     */
    public function setCurrentContacts($currentContacts)
    {
      $this->currentContacts = $currentContacts;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxContacts()
    {
      return $this->maxContacts;
    }

    /**
     * @param int $maxContacts
     * @return generalSettings
     */
    public function setMaxContacts($maxContacts)
    {
      $this->maxContacts = $maxContacts;
      return $this;
    }

    /**
     * @return int
     */
    public function getCurrentMonthlyEmails()
    {
      return $this->currentMonthlyEmails;
    }

    /**
     * @param int $currentMonthlyEmails
     * @return generalSettings
     */
    public function setCurrentMonthlyEmails($currentMonthlyEmails)
    {
      $this->currentMonthlyEmails = $currentMonthlyEmails;
      return $this;
    }

    /**
     * @return int
     */
    public function getCurrentHostingSize()
    {
      return $this->currentHostingSize;
    }

    /**
     * @param int $currentHostingSize
     * @return generalSettings
     */
    public function setCurrentHostingSize($currentHostingSize)
    {
      $this->currentHostingSize = $currentHostingSize;
      return $this;
    }

    /**
     * @return int
     */
    public function getMaxHostingSize()
    {
      return $this->maxHostingSize;
    }

    /**
     * @param int $maxHostingSize
     * @return generalSettings
     */
    public function setMaxHostingSize($maxHostingSize)
    {
      $this->maxHostingSize = $maxHostingSize;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAgencyTemplateuploadPerm()
    {
      return $this->agencyTemplateuploadPerm;
    }

    /**
     * @param boolean $agencyTemplateuploadPerm
     * @return generalSettings
     */
    public function setAgencyTemplateuploadPerm($agencyTemplateuploadPerm)
    {
      $this->agencyTemplateuploadPerm = $agencyTemplateuploadPerm;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDefaultTemplates()
    {
      return $this->defaultTemplates;
    }

    /**
     * @param boolean $defaultTemplates
     * @return generalSettings
     */
    public function setDefaultTemplates($defaultTemplates)
    {
      $this->defaultTemplates = $defaultTemplates;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getEnableInboxPreviews()
    {
      return $this->enableInboxPreviews;
    }

    /**
     * @param boolean $enableInboxPreviews
     * @return generalSettings
     */
    public function setEnableInboxPreviews($enableInboxPreviews)
    {
      $this->enableInboxPreviews = $enableInboxPreviews;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowCustomizedBranding()
    {
      return $this->allowCustomizedBranding;
    }

    /**
     * @param boolean $allowCustomizedBranding
     * @return generalSettings
     */
    public function setAllowCustomizedBranding($allowCustomizedBranding)
    {
      $this->allowCustomizedBranding = $allowCustomizedBranding;
      return $this;
    }

    /**
     * @return int
     */
    public function getBounceLimit()
    {
      return $this->bounceLimit;
    }

    /**
     * @param int $bounceLimit
     * @return generalSettings
     */
    public function setBounceLimit($bounceLimit)
    {
      $this->bounceLimit = $bounceLimit;
      return $this;
    }

}
