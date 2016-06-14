<?php
namespace App\Library\Bronto;
class deliveryObject
{

    /**
     * @var string $id
     */
    public $id = null;

    /**
     * @var \DateTime $start
     */
    public $start = null;

    /**
     * @var string $messageId
     */
    public $messageId = null;

    /**
     * @var string $status
     */
    public $status = null;

    /**
     * @var string $type
     */
    public $type = null;

    /**
     * @var string $fromEmail
     */
    public $fromEmail = null;

    /**
     * @var string $fromName
     */
    public $fromName = null;

    /**
     * @var boolean $authentication
     */
    public $authentication = null;

    /**
     * @var boolean $replyTracking
     */
    public $replyTracking = null;



    /**
     * @var int $throttle
     */
    public $throttle = null;

    /**
     * @var boolean $fatigueOverride
     */
    public $fatigueOverride = null;

    /**
     * @var int $numSends
     */
    public $numSends = null;

    /**
     * @var int $numDeliveries
     */
    public $numDeliveries = null;

    /**
     * @var int $numHardBadEmail
     */
    public $numHardBadEmail = null;

    /**
     * @var int $numHardDestUnreach
     */
    public $numHardDestUnreach = null;

    /**
     * @var int $numHardMessageContent
     */
    public $numHardMessageContent = null;

    /**
     * @var int $numHardBounces
     */
    public $numHardBounces = null;

    /**
     * @var int $numSoftBadEmail
     */
    public $numSoftBadEmail = null;

    /**
     * @var int $numSoftDestUnreach
     */
    public $numSoftDestUnreach = null;

    /**
     * @var int $numSoftMessageContent
     */
    public $numSoftMessageContent = null;

    /**
     * @var int $numSoftBounces
     */
    public $numSoftBounces = null;

    /**
     * @var int $numOtherBounces
     */
    public $numOtherBounces = null;

    /**
     * @var int $numBounces
     */
    public $numBounces = null;

    /**
     * @var int $uniqOpens
     */
    public $uniqOpens = null;

    /**
     * @var int $numOpens
     */
    public $numOpens = null;

    /**
     * @var float $avgOpens
     */
    public $avgOpens = null;

    /**
     * @var int $uniqClicks
     */
    public $uniqClicks = null;

    /**
     * @var int $numClicks
     */
    public $numClicks = null;

    /**
     * @var float $avgClicks
     */
    public $avgClicks = null;

    /**
     * @var int $uniqConversions
     */
    public $uniqConversions = null;

    /**
     * @var int $numConversions
     */
    public $numConversions = null;

    /**
     * @var float $avgConversions
     */
    public $avgConversions = null;

    /**
     * @var float $revenue
     */
    public $revenue = null;

    /**
     * @var int $numSurveyResponses
     */
    public $numSurveyResponses = null;

    /**
     * @var int $numFriendForwards
     */
    public $numFriendForwards = null;

    /**
     * @var int $numContactUpdates
     */
    public $numContactUpdates = null;

    /**
     * @var int $numUnsubscribesByPrefs
     */
    public $numUnsubscribesByPrefs = null;

    /**
     * @var int $numUnsubscribesByComplaint
     */
    public $numUnsubscribesByComplaint = null;

    /**
     * @var int $numContactLoss
     */
    public $numContactLoss = null;

    /**
     * @var int $numContactLossBounces
     */
    public $numContactLossBounces = null;

    /**
     * @var float $deliveryRate
     */
    public $deliveryRate = null;

    /**
     * @var float $openRate
     */
    public $openRate = null;

    /**
     * @var float $clickRate
     */
    public $clickRate = null;

    /**
     * @var float $clickThroughRate
     */
    public $clickThroughRate = null;

    /**
     * @var float $conversionRate
     */
    public $conversionRate = null;

    /**
     * @var float $bounceRate
     */
    public $bounceRate = null;

    /**
     * @var float $complaintRate
     */
    public $complaintRate = null;

    /**
     * @var float $contactLossRate
     */
    public $contactLossRate = null;

    /**
     * @var int $numSocialShares
     */
    public $numSocialShares = null;

    /**
     * @var int $numSharesFacebook
     */
    public $numSharesFacebook = null;

    /**
     * @var int $numSharesTwitter
     */
    public $numSharesTwitter = null;

    /**
     * @var int $numSharesLinkedIn
     */
    public $numSharesLinkedIn = null;

    /**
     * @var int $numSharesDigg
     */
    public $numSharesDigg = null;

    /**
     * @var int $numSharesMySpace
     */
    public $numSharesMySpace = null;

    /**
     * @var int $numViewsFacebook
     */
    public $numViewsFacebook = null;

    /**
     * @var int $numViewsTwitter
     */
    public $numViewsTwitter = null;

    /**
     * @var int $numViewsLinkedIn
     */
    public $numViewsLinkedIn = null;

    /**
     * @var int $numViewsDigg
     */
    public $numViewsDigg = null;

    /**
     * @var int $numViewsMySpace
     */
    public $numViewsMySpace = null;

    /**
     * @var int $numSocialViews
     */
    public $numSocialViews = null;

    /**
     * @param string $id
     * @param \DateTime $start
     * @param string $messageId
     * @param string $status
     * @param string $type
     * @param string $fromEmail
     * @param string $fromName
     * @param string $replyEmail
     * @param boolean $authentication
     * @param boolean $replyTracking
     * @param string $messageRuleId
     * @param boolean $optin
     * @param int $throttle
     * @param boolean $fatigueOverride
     * @param remailObject $remail
     * @param int $numSends
     * @param int $numDeliveries
     * @param int $numHardBadEmail
     * @param int $numHardDestUnreach
     * @param int $numHardMessageContent
     * @param int $numHardBounces
     * @param int $numSoftBadEmail
     * @param int $numSoftDestUnreach
     * @param int $numSoftMessageContent
     * @param int $numSoftBounces
     * @param int $numOtherBounces
     * @param int $numBounces
     * @param int $uniqOpens
     * @param int $numOpens
     * @param float $avgOpens
     * @param int $uniqClicks
     * @param int $numClicks
     * @param float $avgClicks
     * @param int $uniqConversions
     * @param int $numConversions
     * @param float $avgConversions
     * @param float $revenue
     * @param int $numSurveyResponses
     * @param int $numFriendForwards
     * @param int $numContactUpdates
     * @param int $numUnsubscribesByPrefs
     * @param int $numUnsubscribesByComplaint
     * @param int $numContactLoss
     * @param int $numContactLossBounces
     * @param float $deliveryRate
     * @param float $openRate
     * @param float $clickRate
     * @param float $clickThroughRate
     * @param float $conversionRate
     * @param float $bounceRate
     * @param float $complaintRate
     * @param float $contactLossRate
     * @param int $numSocialShares
     * @param int $numSharesFacebook
     * @param int $numSharesTwitter
     * @param int $numSharesLinkedIn
     * @param int $numSharesDigg
     * @param int $numSharesMySpace
     * @param int $numViewsFacebook
     * @param int $numViewsTwitter
     * @param int $numViewsLinkedIn
     * @param int $numViewsDigg
     * @param int $numViewsMySpace
     * @param int $numSocialViews
     * @param string $cartId
     * @param string $orderId
     */
    public function __construct($id, \DateTime $start, $messageId, $status, $type, $fromEmail, $fromName, $replyEmail, $authentication, $replyTracking, $messageRuleId, $optin, $throttle, $fatigueOverride, $remail, $numSends, $numDeliveries, $numHardBadEmail, $numHardDestUnreach, $numHardMessageContent, $numHardBounces, $numSoftBadEmail, $numSoftDestUnreach, $numSoftMessageContent, $numSoftBounces, $numOtherBounces, $numBounces, $uniqOpens, $numOpens, $avgOpens, $uniqClicks, $numClicks, $avgClicks, $uniqConversions, $numConversions, $avgConversions, $revenue, $numSurveyResponses, $numFriendForwards, $numContactUpdates, $numUnsubscribesByPrefs, $numUnsubscribesByComplaint, $numContactLoss, $numContactLossBounces, $deliveryRate, $openRate, $clickRate, $clickThroughRate, $conversionRate, $bounceRate, $complaintRate, $contactLossRate, $numSocialShares, $numSharesFacebook, $numSharesTwitter, $numSharesLinkedIn, $numSharesDigg, $numSharesMySpace, $numViewsFacebook, $numViewsTwitter, $numViewsLinkedIn, $numViewsDigg, $numViewsMySpace, $numSocialViews, $cartId, $orderId)
    {
      $this->id = $id;
      $this->start = $start->format(\DateTime::ATOM);
      $this->messageId = $messageId;
      $this->status = $status;
      $this->type = $type;
      $this->fromEmail = $fromEmail;
      $this->fromName = $fromName;
      $this->replyEmail = $replyEmail;
      $this->authentication = $authentication;
      $this->replyTracking = $replyTracking;
      $this->messageRuleId = $messageRuleId;
      $this->optin = $optin;
      $this->throttle = $throttle;
      $this->fatigueOverride = $fatigueOverride;
      $this->remail = $remail;
      $this->numSends = $numSends;
      $this->numDeliveries = $numDeliveries;
      $this->numHardBadEmail = $numHardBadEmail;
      $this->numHardDestUnreach = $numHardDestUnreach;
      $this->numHardMessageContent = $numHardMessageContent;
      $this->numHardBounces = $numHardBounces;
      $this->numSoftBadEmail = $numSoftBadEmail;
      $this->numSoftDestUnreach = $numSoftDestUnreach;
      $this->numSoftMessageContent = $numSoftMessageContent;
      $this->numSoftBounces = $numSoftBounces;
      $this->numOtherBounces = $numOtherBounces;
      $this->numBounces = $numBounces;
      $this->uniqOpens = $uniqOpens;
      $this->numOpens = $numOpens;
      $this->avgOpens = $avgOpens;
      $this->uniqClicks = $uniqClicks;
      $this->numClicks = $numClicks;
      $this->avgClicks = $avgClicks;
      $this->uniqConversions = $uniqConversions;
      $this->numConversions = $numConversions;
      $this->avgConversions = $avgConversions;
      $this->revenue = $revenue;
      $this->numSurveyResponses = $numSurveyResponses;
      $this->numFriendForwards = $numFriendForwards;
      $this->numContactUpdates = $numContactUpdates;
      $this->numUnsubscribesByPrefs = $numUnsubscribesByPrefs;
      $this->numUnsubscribesByComplaint = $numUnsubscribesByComplaint;
      $this->numContactLoss = $numContactLoss;
      $this->numContactLossBounces = $numContactLossBounces;
      $this->deliveryRate = $deliveryRate;
      $this->openRate = $openRate;
      $this->clickRate = $clickRate;
      $this->clickThroughRate = $clickThroughRate;
      $this->conversionRate = $conversionRate;
      $this->bounceRate = $bounceRate;
      $this->complaintRate = $complaintRate;
      $this->contactLossRate = $contactLossRate;
      $this->numSocialShares = $numSocialShares;
      $this->numSharesFacebook = $numSharesFacebook;
      $this->numSharesTwitter = $numSharesTwitter;
      $this->numSharesLinkedIn = $numSharesLinkedIn;
      $this->numSharesDigg = $numSharesDigg;
      $this->numSharesMySpace = $numSharesMySpace;
      $this->numViewsFacebook = $numViewsFacebook;
      $this->numViewsTwitter = $numViewsTwitter;
      $this->numViewsLinkedIn = $numViewsLinkedIn;
      $this->numViewsDigg = $numViewsDigg;
      $this->numViewsMySpace = $numViewsMySpace;
      $this->numSocialViews = $numSocialViews;
      $this->cartId = $cartId;
      $this->orderId = $orderId;
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
     * @return deliveryObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
      if ($this->start == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->start);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $start
     * @return deliveryObject
     */
    public function setStart(\DateTime $start)
    {
      $this->start = $start->format(\DateTime::ATOM);
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
     * @return deliveryObject
     */
    public function setMessageId($messageId)
    {
      $this->messageId = $messageId;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return deliveryObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
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
     * @return deliveryObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
      return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     * @return deliveryObject
     */
    public function setFromEmail($fromEmail)
    {
      $this->fromEmail = $fromEmail;
      return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
      return $this->fromName;
    }

    /**
     * @param string $fromName
     * @return deliveryObject
     */
    public function setFromName($fromName)
    {
      $this->fromName = $fromName;
      return $this;
    }

    /**
     * @return string
     */
    public function getReplyEmail()
    {
      return $this->replyEmail;
    }

    /**
     * @param string $replyEmail
     * @return deliveryObject
     */
    public function setReplyEmail($replyEmail)
    {
      $this->replyEmail = $replyEmail;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param boolean $authentication
     * @return deliveryObject
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReplyTracking()
    {
      return $this->replyTracking;
    }

    /**
     * @param boolean $replyTracking
     * @return deliveryObject
     */
    public function setReplyTracking($replyTracking)
    {
      $this->replyTracking = $replyTracking;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageRuleId()
    {
      return $this->messageRuleId;
    }

    /**
     * @param string $messageRuleId
     * @return deliveryObject
     */
    public function setMessageRuleId($messageRuleId)
    {
      $this->messageRuleId = $messageRuleId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOptin()
    {
      return $this->optin;
    }

    /**
     * @param boolean $optin
     * @return deliveryObject
     */
    public function setOptin($optin)
    {
      $this->optin = $optin;
      return $this;
    }

    /**
     * @return int
     */
    public function getThrottle()
    {
      return $this->throttle;
    }

    /**
     * @param int $throttle
     * @return deliveryObject
     */
    public function setThrottle($throttle)
    {
      $this->throttle = $throttle;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getFatigueOverride()
    {
      return $this->fatigueOverride;
    }

    /**
     * @param boolean $fatigueOverride
     * @return deliveryObject
     */
    public function setFatigueOverride($fatigueOverride)
    {
      $this->fatigueOverride = $fatigueOverride;
      return $this;
    }

    /**
     * @return messageContentObject[]
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param messageContentObject[] $content
     * @return deliveryObject
     */
    public function setContent(array $content)
    {
      $this->content = $content;
      return $this;
    }

    /**
     * @return deliveryRecipientObject[]
     */
    public function getRecipients()
    {
      return $this->recipients;
    }

    /**
     * @param deliveryRecipientObject[] $recipients
     * @return deliveryObject
     */
    public function setRecipients(array $recipients)
    {
      $this->recipients = $recipients;
      return $this;
    }

    /**
     * @return messageFieldObject[]
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param messageFieldObject[] $fields
     * @return deliveryObject
     */
    public function setFields(array $fields)
    {
      $this->fields = $fields;
      return $this;
    }

    /**
     * @return deliveryProductObject[]
     */
    public function getProducts()
    {
      return $this->products;
    }

    /**
     * @param deliveryProductObject[] $products
     * @return deliveryObject
     */
    public function setProducts(array $products)
    {
      $this->products = $products;
      return $this;
    }

    /**
     * @return remailObject
     */
    public function getRemail()
    {
      return $this->remail;
    }

    /**
     * @param remailObject $remail
     * @return deliveryObject
     */
    public function setRemail($remail)
    {
      $this->remail = $remail;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSends()
    {
      return $this->numSends;
    }

    /**
     * @param int $numSends
     * @return deliveryObject
     */
    public function setNumSends($numSends)
    {
      $this->numSends = $numSends;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumDeliveries()
    {
      return $this->numDeliveries;
    }

    /**
     * @param int $numDeliveries
     * @return deliveryObject
     */
    public function setNumDeliveries($numDeliveries)
    {
      $this->numDeliveries = $numDeliveries;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumHardBadEmail()
    {
      return $this->numHardBadEmail;
    }

    /**
     * @param int $numHardBadEmail
     * @return deliveryObject
     */
    public function setNumHardBadEmail($numHardBadEmail)
    {
      $this->numHardBadEmail = $numHardBadEmail;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumHardDestUnreach()
    {
      return $this->numHardDestUnreach;
    }

    /**
     * @param int $numHardDestUnreach
     * @return deliveryObject
     */
    public function setNumHardDestUnreach($numHardDestUnreach)
    {
      $this->numHardDestUnreach = $numHardDestUnreach;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumHardMessageContent()
    {
      return $this->numHardMessageContent;
    }

    /**
     * @param int $numHardMessageContent
     * @return deliveryObject
     */
    public function setNumHardMessageContent($numHardMessageContent)
    {
      $this->numHardMessageContent = $numHardMessageContent;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumHardBounces()
    {
      return $this->numHardBounces;
    }

    /**
     * @param int $numHardBounces
     * @return deliveryObject
     */
    public function setNumHardBounces($numHardBounces)
    {
      $this->numHardBounces = $numHardBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSoftBadEmail()
    {
      return $this->numSoftBadEmail;
    }

    /**
     * @param int $numSoftBadEmail
     * @return deliveryObject
     */
    public function setNumSoftBadEmail($numSoftBadEmail)
    {
      $this->numSoftBadEmail = $numSoftBadEmail;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSoftDestUnreach()
    {
      return $this->numSoftDestUnreach;
    }

    /**
     * @param int $numSoftDestUnreach
     * @return deliveryObject
     */
    public function setNumSoftDestUnreach($numSoftDestUnreach)
    {
      $this->numSoftDestUnreach = $numSoftDestUnreach;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSoftMessageContent()
    {
      return $this->numSoftMessageContent;
    }

    /**
     * @param int $numSoftMessageContent
     * @return deliveryObject
     */
    public function setNumSoftMessageContent($numSoftMessageContent)
    {
      $this->numSoftMessageContent = $numSoftMessageContent;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSoftBounces()
    {
      return $this->numSoftBounces;
    }

    /**
     * @param int $numSoftBounces
     * @return deliveryObject
     */
    public function setNumSoftBounces($numSoftBounces)
    {
      $this->numSoftBounces = $numSoftBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumOtherBounces()
    {
      return $this->numOtherBounces;
    }

    /**
     * @param int $numOtherBounces
     * @return deliveryObject
     */
    public function setNumOtherBounces($numOtherBounces)
    {
      $this->numOtherBounces = $numOtherBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumBounces()
    {
      return $this->numBounces;
    }

    /**
     * @param int $numBounces
     * @return deliveryObject
     */
    public function setNumBounces($numBounces)
    {
      $this->numBounces = $numBounces;
      return $this;
    }

    /**
     * @return int
     */
    public function getUniqOpens()
    {
      return $this->uniqOpens;
    }

    /**
     * @param int $uniqOpens
     * @return deliveryObject
     */
    public function setUniqOpens($uniqOpens)
    {
      $this->uniqOpens = $uniqOpens;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumOpens()
    {
      return $this->numOpens;
    }

    /**
     * @param int $numOpens
     * @return deliveryObject
     */
    public function setNumOpens($numOpens)
    {
      $this->numOpens = $numOpens;
      return $this;
    }

    /**
     * @return float
     */
    public function getAvgOpens()
    {
      return $this->avgOpens;
    }

    /**
     * @param float $avgOpens
     * @return deliveryObject
     */
    public function setAvgOpens($avgOpens)
    {
      $this->avgOpens = $avgOpens;
      return $this;
    }

    /**
     * @return int
     */
    public function getUniqClicks()
    {
      return $this->uniqClicks;
    }

    /**
     * @param int $uniqClicks
     * @return deliveryObject
     */
    public function setUniqClicks($uniqClicks)
    {
      $this->uniqClicks = $uniqClicks;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumClicks()
    {
      return $this->numClicks;
    }

    /**
     * @param int $numClicks
     * @return deliveryObject
     */
    public function setNumClicks($numClicks)
    {
      $this->numClicks = $numClicks;
      return $this;
    }

    /**
     * @return float
     */
    public function getAvgClicks()
    {
      return $this->avgClicks;
    }

    /**
     * @param float $avgClicks
     * @return deliveryObject
     */
    public function setAvgClicks($avgClicks)
    {
      $this->avgClicks = $avgClicks;
      return $this;
    }

    /**
     * @return int
     */
    public function getUniqConversions()
    {
      return $this->uniqConversions;
    }

    /**
     * @param int $uniqConversions
     * @return deliveryObject
     */
    public function setUniqConversions($uniqConversions)
    {
      $this->uniqConversions = $uniqConversions;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumConversions()
    {
      return $this->numConversions;
    }

    /**
     * @param int $numConversions
     * @return deliveryObject
     */
    public function setNumConversions($numConversions)
    {
      $this->numConversions = $numConversions;
      return $this;
    }

    /**
     * @return float
     */
    public function getAvgConversions()
    {
      return $this->avgConversions;
    }

    /**
     * @param float $avgConversions
     * @return deliveryObject
     */
    public function setAvgConversions($avgConversions)
    {
      $this->avgConversions = $avgConversions;
      return $this;
    }

    /**
     * @return float
     */
    public function getRevenue()
    {
      return $this->revenue;
    }

    /**
     * @param float $revenue
     * @return deliveryObject
     */
    public function setRevenue($revenue)
    {
      $this->revenue = $revenue;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSurveyResponses()
    {
      return $this->numSurveyResponses;
    }

    /**
     * @param int $numSurveyResponses
     * @return deliveryObject
     */
    public function setNumSurveyResponses($numSurveyResponses)
    {
      $this->numSurveyResponses = $numSurveyResponses;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumFriendForwards()
    {
      return $this->numFriendForwards;
    }

    /**
     * @param int $numFriendForwards
     * @return deliveryObject
     */
    public function setNumFriendForwards($numFriendForwards)
    {
      $this->numFriendForwards = $numFriendForwards;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumContactUpdates()
    {
      return $this->numContactUpdates;
    }

    /**
     * @param int $numContactUpdates
     * @return deliveryObject
     */
    public function setNumContactUpdates($numContactUpdates)
    {
      $this->numContactUpdates = $numContactUpdates;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumUnsubscribesByPrefs()
    {
      return $this->numUnsubscribesByPrefs;
    }

    /**
     * @param int $numUnsubscribesByPrefs
     * @return deliveryObject
     */
    public function setNumUnsubscribesByPrefs($numUnsubscribesByPrefs)
    {
      $this->numUnsubscribesByPrefs = $numUnsubscribesByPrefs;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumUnsubscribesByComplaint()
    {
      return $this->numUnsubscribesByComplaint;
    }

    /**
     * @param int $numUnsubscribesByComplaint
     * @return deliveryObject
     */
    public function setNumUnsubscribesByComplaint($numUnsubscribesByComplaint)
    {
      $this->numUnsubscribesByComplaint = $numUnsubscribesByComplaint;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumContactLoss()
    {
      return $this->numContactLoss;
    }

    /**
     * @param int $numContactLoss
     * @return deliveryObject
     */
    public function setNumContactLoss($numContactLoss)
    {
      $this->numContactLoss = $numContactLoss;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumContactLossBounces()
    {
      return $this->numContactLossBounces;
    }

    /**
     * @param int $numContactLossBounces
     * @return deliveryObject
     */
    public function setNumContactLossBounces($numContactLossBounces)
    {
      $this->numContactLossBounces = $numContactLossBounces;
      return $this;
    }

    /**
     * @return float
     */
    public function getDeliveryRate()
    {
      return $this->deliveryRate;
    }

    /**
     * @param float $deliveryRate
     * @return deliveryObject
     */
    public function setDeliveryRate($deliveryRate)
    {
      $this->deliveryRate = $deliveryRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getOpenRate()
    {
      return $this->openRate;
    }

    /**
     * @param float $openRate
     * @return deliveryObject
     */
    public function setOpenRate($openRate)
    {
      $this->openRate = $openRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getClickRate()
    {
      return $this->clickRate;
    }

    /**
     * @param float $clickRate
     * @return deliveryObject
     */
    public function setClickRate($clickRate)
    {
      $this->clickRate = $clickRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getClickThroughRate()
    {
      return $this->clickThroughRate;
    }

    /**
     * @param float $clickThroughRate
     * @return deliveryObject
     */
    public function setClickThroughRate($clickThroughRate)
    {
      $this->clickThroughRate = $clickThroughRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getConversionRate()
    {
      return $this->conversionRate;
    }

    /**
     * @param float $conversionRate
     * @return deliveryObject
     */
    public function setConversionRate($conversionRate)
    {
      $this->conversionRate = $conversionRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getBounceRate()
    {
      return $this->bounceRate;
    }

    /**
     * @param float $bounceRate
     * @return deliveryObject
     */
    public function setBounceRate($bounceRate)
    {
      $this->bounceRate = $bounceRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getComplaintRate()
    {
      return $this->complaintRate;
    }

    /**
     * @param float $complaintRate
     * @return deliveryObject
     */
    public function setComplaintRate($complaintRate)
    {
      $this->complaintRate = $complaintRate;
      return $this;
    }

    /**
     * @return float
     */
    public function getContactLossRate()
    {
      return $this->contactLossRate;
    }

    /**
     * @param float $contactLossRate
     * @return deliveryObject
     */
    public function setContactLossRate($contactLossRate)
    {
      $this->contactLossRate = $contactLossRate;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSocialShares()
    {
      return $this->numSocialShares;
    }

    /**
     * @param int $numSocialShares
     * @return deliveryObject
     */
    public function setNumSocialShares($numSocialShares)
    {
      $this->numSocialShares = $numSocialShares;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSharesFacebook()
    {
      return $this->numSharesFacebook;
    }

    /**
     * @param int $numSharesFacebook
     * @return deliveryObject
     */
    public function setNumSharesFacebook($numSharesFacebook)
    {
      $this->numSharesFacebook = $numSharesFacebook;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSharesTwitter()
    {
      return $this->numSharesTwitter;
    }

    /**
     * @param int $numSharesTwitter
     * @return deliveryObject
     */
    public function setNumSharesTwitter($numSharesTwitter)
    {
      $this->numSharesTwitter = $numSharesTwitter;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSharesLinkedIn()
    {
      return $this->numSharesLinkedIn;
    }

    /**
     * @param int $numSharesLinkedIn
     * @return deliveryObject
     */
    public function setNumSharesLinkedIn($numSharesLinkedIn)
    {
      $this->numSharesLinkedIn = $numSharesLinkedIn;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSharesDigg()
    {
      return $this->numSharesDigg;
    }

    /**
     * @param int $numSharesDigg
     * @return deliveryObject
     */
    public function setNumSharesDigg($numSharesDigg)
    {
      $this->numSharesDigg = $numSharesDigg;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSharesMySpace()
    {
      return $this->numSharesMySpace;
    }

    /**
     * @param int $numSharesMySpace
     * @return deliveryObject
     */
    public function setNumSharesMySpace($numSharesMySpace)
    {
      $this->numSharesMySpace = $numSharesMySpace;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumViewsFacebook()
    {
      return $this->numViewsFacebook;
    }

    /**
     * @param int $numViewsFacebook
     * @return deliveryObject
     */
    public function setNumViewsFacebook($numViewsFacebook)
    {
      $this->numViewsFacebook = $numViewsFacebook;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumViewsTwitter()
    {
      return $this->numViewsTwitter;
    }

    /**
     * @param int $numViewsTwitter
     * @return deliveryObject
     */
    public function setNumViewsTwitter($numViewsTwitter)
    {
      $this->numViewsTwitter = $numViewsTwitter;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumViewsLinkedIn()
    {
      return $this->numViewsLinkedIn;
    }

    /**
     * @param int $numViewsLinkedIn
     * @return deliveryObject
     */
    public function setNumViewsLinkedIn($numViewsLinkedIn)
    {
      $this->numViewsLinkedIn = $numViewsLinkedIn;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumViewsDigg()
    {
      return $this->numViewsDigg;
    }

    /**
     * @param int $numViewsDigg
     * @return deliveryObject
     */
    public function setNumViewsDigg($numViewsDigg)
    {
      $this->numViewsDigg = $numViewsDigg;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumViewsMySpace()
    {
      return $this->numViewsMySpace;
    }

    /**
     * @param int $numViewsMySpace
     * @return deliveryObject
     */
    public function setNumViewsMySpace($numViewsMySpace)
    {
      $this->numViewsMySpace = $numViewsMySpace;
      return $this;
    }

    /**
     * @return int
     */
    public function getNumSocialViews()
    {
      return $this->numSocialViews;
    }

    /**
     * @param int $numSocialViews
     * @return deliveryObject
     */
    public function setNumSocialViews($numSocialViews)
    {
      $this->numSocialViews = $numSocialViews;
      return $this;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
      return $this->cartId;
    }

    /**
     * @param string $cartId
     * @return deliveryObject
     */
    public function setCartId($cartId)
    {
      $this->cartId = $cartId;
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
     * @return deliveryObject
     */
    public function setOrderId($orderId)
    {
      $this->orderId = $orderId;
      return $this;
    }

    public function toArray(){
    return (array) $this;
}

}
