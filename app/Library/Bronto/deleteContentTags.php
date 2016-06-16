<?php

class deleteContentTags
{

    /**
     * @var contentTagObject[] $contentTags
     */
    protected $contentTags = null;

    /**
     * @param contentTagObject[] $contentTags
     */
    public function __construct(array $contentTags)
    {
      $this->contentTags = $contentTags;
    }

    /**
     * @return contentTagObject[]
     */
    public function getContentTags()
    {
      return $this->contentTags;
    }

    /**
     * @param contentTagObject[] $contentTags
     * @return deleteContentTags
     */
    public function setContentTags(array $contentTags)
    {
      $this->contentTags = $contentTags;
      return $this;
    }

}
