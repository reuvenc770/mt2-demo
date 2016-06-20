<?php

class deliveryGroupObject
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
     * @var string $visibility
     */
    protected $visibility = null;

    /**
     * @var int $deliveryCount
     */
    protected $deliveryCount = null;

    /**
     * @var \DateTime $createdDate
     */
    protected $createdDate = null;

    /**
     * @var string[] $deliveryIds
     */
    protected $deliveryIds = null;

    /**
     * @var string[] $messageRuleIds
     */
    protected $messageRuleIds = null;

    /**
     * @var string[] $messageIds
     */
    protected $messageIds = null;

    /**
     * @var int $numSends
     */
    protected $numSends = null;

    /**
     * @var int $numDeliveries
     */
    protected $numDeliveries = null;

    /**
     * @var int $numHardBadEmail
     */
    protected $numHardBadEmail = null;

    /**
     * @var int $numHardDestUnreach
     */
    protected $numHardDestUnreach = null;

    /**
     * @var int $numHardMessageContent
     */
    protected $numHardMessageContent = null;

    /**
     * @var int $numHardBounces
     */
    protected $numHardBounces = null;

    /**
     * @var int $numSoftBadEmail
     */
    protected $numSoftBadEmail = null;

    /**
     * @var int $numSoftDestUnreach
     */
    protected $numSoftDestUnreach = null;

    /**
     * @var int $numSoftMessageContent
     */
    protected $numSoftMessageContent = null;

    /**
     * @var int $numSoftBounces
     */
    protected $numSoftBounces = null;

    /**
     * @var int $numOtherBounces
     */
    protected $numOtherBounces = null;

    /**
     * @var int $numBounces
     */
    protected $numBounces = null;

    /**
     * @var int $uniqOpens
     */
    protected $uniqOpens = null;

    /**
     * @var int $numOpens
     */
    protected $numOpens = null;

    /**
     * @var float $avgOpens
     */
    protected $avgOpens = null;

    /**
     * @var int $uniqClicks
     */
    protected $uniqClicks = null;

    /**
     * @var int $numClicks
     */
    protected $numClicks = null;

    /**
     * @var float $avgClicks
     */
    protected $avgClicks = null;

    /**
     * @var int $uniqConversions
     */
    protected $uniqConversions = null;

    /**
     * @var int $numConversions
     */
    protected $numConversions = null;

    /**
     * @var float $avgConversions
     */
    protected $avgConversions = null;

    /**
     * @var float $revenue
     */
    protected $revenue = null;

    /**
     * @var int $numSurveyResponses
     */
    protected $numSurveyResponses = null;

    /**
     * @var int $numFriendForwards
     */
    protected $numFriendForwards = null;

    /**
     * @var int $numContactUpdates
     */
    protected $numContactUpdates = null;

    /**
     * @var int $numUnsubscribesByPrefs
     */
    protected $numUnsubscribesByPrefs = null;

    /**
     * @var int $numUnsubscribesByComplaint
     */
    protected $numUnsubscribesByComplaint = null;

    /**
     * @var int $numContactLossBounces
     */
    protected $numContactLossBounces = null;

    /**
     * @var int $numContactLoss
     */
    protected $numContactLoss = null;

    /**
     * @var float $deliveryRate
     */
    protected $deliveryRate = null;

    /**
     * @var float $openRate
     */
    protected $openRate = null;

    /**
     * @var float $clickRate
     */
    protected $clickRate = null;

    /**
     * @var float $clickThroughRate
     */
    protected $clickThroughRate = null;

    /**
     * @var float $conversionRate
     */
    protected $conversionRate = null;

    /**
     * @var float $bounceRate
     */
    protected $bounceRate = null;

    /**
     * @var float $complaintRate
     */
    protected $complaintRate = null;

    /**
     * @var float $contactLossRate
     */
    protected $contactLossRate = null;

    /**
     * @var int $numSocialShares
     */
    protected $numSocialShares = null;

    /**
     * @var int $numSharesFacebook
     */
    protected $numSharesFacebook = null;

    /**
     * @var int $numSharesTwitter
     */
    protected $numSharesTwitter = null;

    /**
     * @var int $numSharesLinkedIn
     */
    protected $numSharesLinkedIn = null;

    /**
     * @var int $numSharesDigg
     */
    protected $numSharesDigg = null;

    /**
     * @var int $numSharesMySpace
     */
    protected $numSharesMySpace = null;

    /**
     * @var int $numSocialViews
     */
    protected $numSocialViews = null;

    /**
     * @var int $numViewsFacebook
     */
    protected $numViewsFacebook = null;

    /**
     * @var int $numViewsTwitter
     */
    protected $numViewsTwitter = null;

    /**
     * @var int $numViewsLinkedIn
     */
    protected $numViewsLinkedIn = null;

    /**
     * @var int $numViewsDigg
     */
    protected $numViewsDigg = null;

    /**
     * @var int $numViewsMySpace
     */
    protected $numViewsMySpace = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $visibility
     * @param int $deliveryCount
     * @param \DateTime $createdDate
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
     * @param int $numContactLossBounces
     * @param int $numContactLoss
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
     * @param int $numSocialViews
     * @param int $numViewsFacebook
     * @param int $numViewsTwitter
     * @param int $numViewsLinkedIn
     * @param int $numViewsDigg
     * @param int $numViewsMySpace
     */
    public function __construct($id, $name, $visibility, $deliveryCount, \DateTime $createdDate, $numSends, $numDeliveries, $numHardBadEmail, $numHardDestUnreach, $numHardMessageContent, $numHardBounces, $numSoftBadEmail, $numSoftDestUnreach, $numSoftMessageContent, $numSoftBounces, $numOtherBounces, $numBounces, $uniqOpens, $numOpens, $avgOpens, $uniqClicks, $numClicks, $avgClicks, $uniqConversions, $numConversions, $avgConversions, $revenue, $numSurveyResponses, $numFriendForwards, $numContactUpdates, $numUnsubscribesByPrefs, $numUnsubscribesByComplaint, $numContactLossBounces, $numContactLoss, $deliveryRate, $openRate, $clickRate, $clickThroughRate, $conversionRate, $bounceRate, $complaintRate, $contactLossRate, $numSocialShares, $numSharesFacebook, $numSharesTwitter, $numSharesLinkedIn, $numSharesDigg, $numSharesMySpace, $numSocialViews, $numViewsFacebook, $numViewsTwitter, $numViewsLinkedIn, $numViewsDigg, $numViewsMySpace)
    {
      $this->id = $id;
      $this->name = $name;
      $this->visibility = $visibility;
      $this->deliveryCount = $deliveryCount;
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
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
      $this->numContactLossBounces = $numContactLossBounces;
      $this->numContactLoss = $numContactLoss;
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
      $this->numSocialViews = $numSocialViews;
      $this->numViewsFacebook = $numViewsFacebook;
      $this->numViewsTwitter = $numViewsTwitter;
      $this->numViewsLinkedIn = $numViewsLinkedIn;
      $this->numViewsDigg = $numViewsDigg;
      $this->numViewsMySpace = $numViewsMySpace;
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
      return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return deliveryGroupObject
     */
    public function setVisibility($visibility)
    {
      $this->visibility = $visibility;
      return $this;
    }

    /**
     * @return int
     */
    public function getDeliveryCount()
    {
      return $this->deliveryCount;
    }

    /**
     * @param int $deliveryCount
     * @return deliveryGroupObject
     */
    public function setDeliveryCount($deliveryCount)
    {
      $this->deliveryCount = $deliveryCount;
      return $this;
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
     * @return deliveryGroupObject
     */
    public function setCreatedDate(\DateTime $createdDate)
    {
      $this->createdDate = $createdDate->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return string[]
     */
    public function getDeliveryIds()
    {
      return $this->deliveryIds;
    }

    /**
     * @param string[] $deliveryIds
     * @return deliveryGroupObject
     */
    public function setDeliveryIds(array $deliveryIds)
    {
      $this->deliveryIds = $deliveryIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageRuleIds()
    {
      return $this->messageRuleIds;
    }

    /**
     * @param string[] $messageRuleIds
     * @return deliveryGroupObject
     */
    public function setMessageRuleIds(array $messageRuleIds)
    {
      $this->messageRuleIds = $messageRuleIds;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getMessageIds()
    {
      return $this->messageIds;
    }

    /**
     * @param string[] $messageIds
     * @return deliveryGroupObject
     */
    public function setMessageIds(array $messageIds)
    {
      $this->messageIds = $messageIds;
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
     */
    public function setNumUnsubscribesByComplaint($numUnsubscribesByComplaint)
    {
      $this->numUnsubscribesByComplaint = $numUnsubscribesByComplaint;
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
     * @return deliveryGroupObject
     */
    public function setNumContactLossBounces($numContactLossBounces)
    {
      $this->numContactLossBounces = $numContactLossBounces;
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
     * @return deliveryGroupObject
     */
    public function setNumContactLoss($numContactLoss)
    {
      $this->numContactLoss = $numContactLoss;
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
     */
    public function setNumSharesMySpace($numSharesMySpace)
    {
      $this->numSharesMySpace = $numSharesMySpace;
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
     * @return deliveryGroupObject
     */
    public function setNumSocialViews($numSocialViews)
    {
      $this->numSocialViews = $numSocialViews;
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
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
     * @return deliveryGroupObject
     */
    public function setNumViewsMySpace($numViewsMySpace)
    {
      $this->numViewsMySpace = $numViewsMySpace;
      return $this;
    }

}
