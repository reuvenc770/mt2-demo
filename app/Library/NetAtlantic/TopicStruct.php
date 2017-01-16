<?php

namespace App\Library\NetAtlantic;

class TopicStruct
{

    /**
     * @var string $TopicName
     */
    protected $TopicName = null;

    /**
     * @var string $TopicDescription
     */
    protected $TopicDescription = null;

    /**
     * @var string $SiteName
     */
    protected $SiteName = null;

    /**
     * @var boolean $HiddenTopic
     */
    protected $HiddenTopic = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getTopicName()
    {
      return $this->TopicName;
    }

    /**
     * @param string $TopicName
     * @return \App\Library\NetAtlantic\TopicStruct
     */
    public function setTopicName($TopicName)
    {
      $this->TopicName = $TopicName;
      return $this;
    }

    /**
     * @return string
     */
    public function getTopicDescription()
    {
      return $this->TopicDescription;
    }

    /**
     * @param string $TopicDescription
     * @return \App\Library\NetAtlantic\TopicStruct
     */
    public function setTopicDescription($TopicDescription)
    {
      $this->TopicDescription = $TopicDescription;
      return $this;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
      return $this->SiteName;
    }

    /**
     * @param string $SiteName
     * @return \App\Library\NetAtlantic\TopicStruct
     */
    public function setSiteName($SiteName)
    {
      $this->SiteName = $SiteName;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getHiddenTopic()
    {
      return $this->HiddenTopic;
    }

    /**
     * @param boolean $HiddenTopic
     * @return \App\Library\NetAtlantic\TopicStruct
     */
    public function setHiddenTopic($HiddenTopic)
    {
      $this->HiddenTopic = $HiddenTopic;
      return $this;
    }

}
