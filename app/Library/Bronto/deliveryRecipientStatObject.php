<?php

class deliveryRecipientStatObject
{

    /**
     * @var string $deliveryId
     */
    protected $deliveryId = null;

    /**
     * @var string $listId
     */
    protected $listId = null;

    /**
     * @var string $segmentId
     */
    protected $segmentId = null;

    /**
     * @var string $contactId
     */
    protected $contactId = null;

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
     * @var int $numContactLoss
     */
    protected $numContactLoss = null;

    /**
     * @var int $numContactLossBounces
     */
    protected $numContactLossBounces = null;

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
     * @param string $deliveryId
     * @param string $listId
     * @param string $segmentId
     * @param string $contactId
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
     */
    public function __construct($deliveryId, $listId, $segmentId, $contactId, $numSends, $numDeliveries, $numHardBadEmail, $numHardDestUnreach, $numHardMessageContent, $numHardBounces, $numSoftBadEmail, $numSoftDestUnreach, $numSoftMessageContent, $numSoftBounces, $numOtherBounces, $numBounces, $uniqOpens, $numOpens, $avgOpens, $uniqClicks, $numClicks, $avgClicks, $uniqConversions, $numConversions, $avgConversions, $revenue, $numSurveyResponses, $numFriendForwards, $numContactUpdates, $numUnsubscribesByPrefs, $numUnsubscribesByComplaint, $numContactLoss, $numContactLossBounces, $deliveryRate, $openRate, $clickRate, $clickThroughRate, $conversionRate, $bounceRate, $complaintRate, $contactLossRate)
    {
      $this->deliveryId = $deliveryId;
      $this->listId = $listId;
      $this->segmentId = $segmentId;
      $this->contactId = $contactId;
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
     * @return deliveryRecipientStatObject
     */
    public function setDeliveryId($deliveryId)
    {
      $this->deliveryId = $deliveryId;
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
     */
    public function setSegmentId($segmentId)
    {
      $this->segmentId = $segmentId;
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
     * @return deliveryRecipientStatObject
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
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
     * @return deliveryRecipientStatObject
     */
    public function setContactLossRate($contactLossRate)
    {
      $this->contactLossRate = $contactLossRate;
      return $this;
    }

}
