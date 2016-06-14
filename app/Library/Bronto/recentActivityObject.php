<?php
namespace App\Library\Bronto;
class recentActivityObject
{

    /**
     * @var \DateTime $createdDate
     */
    protected $createdDate = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var string $listId
     */
    protected $listId = null;

    /**
     * @var string $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string $keywordId
     */
    protected $keywordId = null;

    /**
     * @var string $messageId
     */
    protected $messageId = null;

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string $workflowId
     */
    protected $workflowId = null;

    /**
     * @var string $activityType
     */
    protected $activityType = null;

    /**
     * @var string $emailAddress
     */
    protected $emailAddress = null;

    /**
     * @var string $mobileNumber
     */
    protected $mobileNumber = null;

    /**
     * @var string $contactStatus
     */
    protected $contactStatus = null;

    /**
     * @var string $messageName
     */
    protected $messageName = null;

    /**
     * @var string $deliveryType
     */
    protected $deliveryType = null;

    /**
     * @var \DateTime $deliveryStart
     */
    protected $deliveryStart = null;

    /**
     * @var string $workflowName
     */
    protected $workflowName = null;

    /**
     * @var string $segmentName
     */
    protected $segmentName = null;

    /**
     * @var string $listName
     */
    protected $listName = null;

    /**
     * @var string $listLabel
     */
    protected $listLabel = null;

    /**
     * @var string $automatorName
     */
    protected $automatorName = null;

    /**
     * @var string $smsKeywordName
     */
    protected $smsKeywordName = null;

    /**
     * @var string $bounceType
     */
    protected $bounceType = null;

    /**
     * @var string $bounceReason
     */
    protected $bounceReason = null;

    /**
     * @var string $skipReason
     */
    protected $skipReason = null;

    /**
     * @var string $linkName
     */
    protected $linkName = null;

    /**
     * @var string $linkUrl
     */
    protected $linkUrl = null;

    /**
     * @var string $orderId
     */
    protected $orderId = null;

    /**
     * @var string $unsubscribeMethod
     */
    protected $unsubscribeMethod = null;

    /**
     * @var string $ftafEmails
     */
    protected $ftafEmails = null;

    /**
     * @var string $socialNetwork
     */
    protected $socialNetwork = null;

    /**
     * @var string $socialActivity
     */
    protected $socialActivity = null;

    /**
     * @var string $webformType
     */
    protected $webformType = null;

    /**
     * @var string $webformAction
     */
    protected $webformAction = null;

    /**
     * @var string $webformName
     */
    protected $webformName = null;

    /**
     * @var string $webformId
     */
    protected $webformId = null;

    /**
     * @param \DateTime $createdDate
     * @param string $contactId
     * @param string $listId
     * @param string $segmentId
     * @param string $keywordId
     * @param string $messageId
     * @param string $deliveryId
     * @param string $workflowId
     * @param string $activityType
     * @param string $emailAddress
     * @param string $mobileNumber
     * @param string $contactStatus
     * @param string $messageName
     * @param string $deliveryType
     * @param \DateTime $deliveryStart
     * @param string $workflowName
     * @param string $segmentName
     * @param string $listName
     * @param string $listLabel
     * @param string $automatorName
     * @param string $smsKeywordName
     * @param string $bounceType
     * @param string $bounceReason
     * @param string $skipReason
     * @param string $linkName
     * @param string $linkUrl
     * @param string $orderId
     * @param string $unsubscribeMethod
     * @param string $ftafEmails
     * @param string $socialNetwork
     * @param string $socialActivity
     * @param string $webformType
     * @param string $webformAction
     * @param string $webformName
     * @param string $webformId
     */
    public function __construct(\DateTime $createdDate, $contactId, $listId, $segmentId, $keywordId, $messageId, $deliveryId, $workflowId, $activityType, $emailAddress, $mobileNumber, $contactStatus, $messageName, $deliveryType, \DateTime $deliveryStart, $workflowName, $segmentName, $listName, $listLabel, $automatorName, $smsKeywordName, $bounceType, $bounceReason, $skipReason, $linkName, $linkUrl, $orderId, $unsubscribeMethod, $ftafEmails, $socialNetwork, $socialActivity, $webformType, $webformAction, $webformName, $webformId)
    {
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      $this->contactId = $contactId;
      $this->listId = $listId;
      $this->segmentId = $segmentId;
      $this->keywordId = $keywordId;
      $this->messageId = $messageId;
      $this->deliveryId = $deliveryId;
      $this->workflowId = $workflowId;
      $this->activityType = $activityType;
      $this->emailAddress = $emailAddress;
      $this->mobileNumber = $mobileNumber;
      $this->contactStatus = $contactStatus;
      $this->messageName = $messageName;
      $this->deliveryType = $deliveryType;
      $this->deliveryStart = $deliveryStart->format(\DateTime::ATOM);
      $this->workflowName = $workflowName;
      $this->segmentName = $segmentName;
      $this->listName = $listName;
      $this->listLabel = $listLabel;
      $this->automatorName = $automatorName;
      $this->smsKeywordName = $smsKeywordName;
      $this->bounceType = $bounceType;
      $this->bounceReason = $bounceReason;
      $this->skipReason = $skipReason;
      $this->linkName = $linkName;
      $this->linkUrl = $linkUrl;
      $this->orderId = $orderId;
      $this->unsubscribeMethod = $unsubscribeMethod;
      $this->ftafEmails = $ftafEmails;
      $this->socialNetwork = $socialNetwork;
      $this->socialActivity = $socialActivity;
      $this->webformType = $webformType;
      $this->webformAction = $webformAction;
      $this->webformName = $webformName;
      $this->webformId = $webformId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
      if ($this->createdDate == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->createdDate);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $createdDate
     * @return recentActivityObject
     */
    public function setCreatedDate(\DateTime $createdDate)
    {
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getContactId()
    {
      return $this->contactId;
    }

    /**
     * @param string $contactId
     * @return recentActivityObject
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getListId()
    {
      return $this->listId;
    }

    /**
     * @param string $listId
     * @return recentActivityObject
     */
    public function setListId($listId)
    {
      $this->listId = $listId;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegmentId()
    {
      return $this->segmentId;
    }

    /**
     * @param string $segmentId
     * @return recentActivityObject
     */
    public function setSegmentId($segmentId)
    {
      $this->segmentId = $segmentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getKeywordId()
    {
      return $this->keywordId;
    }

    /**
     * @param string $keywordId
     * @return recentActivityObject
     */
    public function setKeywordId($keywordId)
    {
      $this->keywordId = $keywordId;
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
     * @return recentActivityObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryId()
    {
      return $this->deliveryId;
    }

    /**
     * @param string $deliveryId
     * @return recentActivityObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
      return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowId()
    {
      return $this->workflowId;
    }

    /**
     * @param string $workflowId
     * @return recentActivityObject
     */
    public function setWorkflowId($workflowId)
    {
      $this->workflowId = $workflowId;
      return $this;
    }

    /**
     * @return string
     */
    public function getActivityType()
    {
      return $this->activityType;
    }

    /**
     * @param string $activityType
     * @return recentActivityObject
     */
    public function setActivityType($activityType)
    {
      $this->activityType = $activityType;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
      return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     * @return recentActivityObject
     */
    public function setEmailAddress($emailAddress)
    {
      $this->emailAddress = $emailAddress;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
      return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     * @return recentActivityObject
     */
    public function setMobileNumber($mobileNumber)
    {
      $this->mobileNumber = $mobileNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getContactStatus()
    {
      return $this->contactStatus;
    }

    /**
     * @param string $contactStatus
     * @return recentActivityObject
     */
    public function setContactStatus($contactStatus)
    {
      $this->contactStatus = $contactStatus;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageName()
    {
      return $this->messageName;
    }

    /**
     * @param string $messageName
     * @return recentActivityObject
     */
    public function setMessageName($messageName)
    {
      $this->messageName = $messageName;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryType()
    {
      return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     * @return recentActivityObject
     */
    public function setDeliveryType($deliveryType)
    {
      $this->deliveryType = $deliveryType;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryStart()
    {
      if ($this->deliveryStart == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->deliveryStart);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $deliveryStart
     * @return recentActivityObject
     */
    public function setDeliveryStart(\DateTime $deliveryStart)
    {
      $this->deliveryStart = $deliveryStart->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowName()
    {
      return $this->workflowName;
    }

    /**
     * @param string $workflowName
     * @return recentActivityObject
     */
    public function setWorkflowName($workflowName)
    {
      $this->workflowName = $workflowName;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegmentName()
    {
      return $this->segmentName;
    }

    /**
     * @param string $segmentName
     * @return recentActivityObject
     */
    public function setSegmentName($segmentName)
    {
      $this->segmentName = $segmentName;
      return $this;
    }

    /**
     * @return string
     */
    public function getListName()
    {
      return $this->listName;
    }

    /**
     * @param string $listName
     * @return recentActivityObject
     */
    public function setListName($listName)
    {
      $this->listName = $listName;
      return $this;
    }

    /**
     * @return string
     */
    public function getListLabel()
    {
      return $this->listLabel;
    }

    /**
     * @param string $listLabel
     * @return recentActivityObject
     */
    public function setListLabel($listLabel)
    {
      $this->listLabel = $listLabel;
      return $this;
    }

    /**
     * @return string
     */
    public function getAutomatorName()
    {
      return $this->automatorName;
    }

    /**
     * @param string $automatorName
     * @return recentActivityObject
     */
    public function setAutomatorName($automatorName)
    {
      $this->automatorName = $automatorName;
      return $this;
    }

    /**
     * @return string
     */
    public function getSmsKeywordName()
    {
      return $this->smsKeywordName;
    }

    /**
     * @param string $smsKeywordName
     * @return recentActivityObject
     */
    public function setSmsKeywordName($smsKeywordName)
    {
      $this->smsKeywordName = $smsKeywordName;
      return $this;
    }

    /**
     * @return string
     */
    public function getBounceType()
    {
      return $this->bounceType;
    }

    /**
     * @param string $bounceType
     * @return recentActivityObject
     */
    public function setBounceType($bounceType)
    {
      $this->bounceType = $bounceType;
      return $this;
    }

    /**
     * @return string
     */
    public function getBounceReason()
    {
      return $this->bounceReason;
    }

    /**
     * @param string $bounceReason
     * @return recentActivityObject
     */
    public function setBounceReason($bounceReason)
    {
      $this->bounceReason = $bounceReason;
      return $this;
    }

    /**
     * @return string
     */
    public function getSkipReason()
    {
      return $this->skipReason;
    }

    /**
     * @param string $skipReason
     * @return recentActivityObject
     */
    public function setSkipReason($skipReason)
    {
      $this->skipReason = $skipReason;
      return $this;
    }

    /**
     * @return string
     */
    public function getLinkName()
    {
      return $this->linkName;
    }

    /**
     * @param string $linkName
     * @return recentActivityObject
     */
    public function setLinkName($linkName)
    {
      $this->linkName = $linkName;
      return $this;
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
      return $this->linkUrl;
    }

    /**
     * @param string $linkUrl
     * @return recentActivityObject
     */
    public function setLinkUrl($linkUrl)
    {
      $this->linkUrl = $linkUrl;
      return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
      return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return recentActivityObject
     */
    public function setOrderId($orderId)
    {
      $this->orderId = $orderId;
      return $this;
    }

    /**
     * @return string
     */
    public function getUnsubscribeMethod()
    {
      return $this->unsubscribeMethod;
    }

    /**
     * @param string $unsubscribeMethod
     * @return recentActivityObject
     */
    public function setUnsubscribeMethod($unsubscribeMethod)
    {
      $this->unsubscribeMethod = $unsubscribeMethod;
      return $this;
    }

    /**
     * @return string
     */
    public function getFtafEmails()
    {
      return $this->ftafEmails;
    }

    /**
     * @param string $ftafEmails
     * @return recentActivityObject
     */
    public function setFtafEmails($ftafEmails)
    {
      $this->ftafEmails = $ftafEmails;
      return $this;
    }

    /**
     * @return string
     */
    public function getSocialNetwork()
    {
      return $this->socialNetwork;
    }

    /**
     * @param string $socialNetwork
     * @return recentActivityObject
     */
    public function setSocialNetwork($socialNetwork)
    {
      $this->socialNetwork = $socialNetwork;
      return $this;
    }

    /**
     * @return string
     */
    public function getSocialActivity()
    {
      return $this->socialActivity;
    }

    /**
     * @param string $socialActivity
     * @return recentActivityObject
     */
    public function setSocialActivity($socialActivity)
    {
      $this->socialActivity = $socialActivity;
      return $this;
    }

    /**
     * @return string
     */
    public function getWebformType()
    {
      return $this->webformType;
    }

    /**
     * @param string $webformType
     * @return recentActivityObject
     */
    public function setWebformType($webformType)
    {
      $this->webformType = $webformType;
      return $this;
    }

    /**
     * @return string
     */
    public function getWebformAction()
    {
      return $this->webformAction;
    }

    /**
     * @param string $webformAction
     * @return recentActivityObject
     */
    public function setWebformAction($webformAction)
    {
      $this->webformAction = $webformAction;
      return $this;
    }

    /**
     * @return string
     */
    public function getWebformName()
    {
      return $this->webformName;
    }

    /**
     * @param string $webformName
     * @return recentActivityObject
     */
    public function setWebformName($webformName)
    {
      $this->webformName = $webformName;
      return $this;
    }

    /**
     * @return string
     */
    public function getWebformId()
    {
      return $this->webformId;
    }

    /**
     * @param string $webformId
     * @return recentActivityObject
     */
    public function setWebformId($webformId)
    {
      $this->webformId = $webformId;
      return $this;
    }

}
