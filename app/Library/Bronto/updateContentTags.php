<?php
namespace App\Library\Bronto;
class updateContentTags
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
     * @return updateContentTags
     */
    public function setContentTags(array $contentTags)
    {
      $this->contentTags = $contentTags;
      return $this;
    }

}
