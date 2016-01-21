<?php
namespace App\Library\Campaigner;
class DeleteMediaFiles
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ArrayOfInt $mediaFileIds
     */
    protected $mediaFileIds = null;

    /**
     * @param Authentication $authentication
     * @param ArrayOfInt $mediaFileIds
     */
    public function __construct($authentication, $mediaFileIds)
    {
      $this->authentication = $authentication;
      $this->mediaFileIds = $mediaFileIds;
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
     * @return DeleteMediaFiles
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ArrayOfInt
     */
    public function getMediaFileIds()
    {
      return $this->mediaFileIds;
    }

    /**
     * @param ArrayOfInt $mediaFileIds
     * @return DeleteMediaFiles
     */
    public function setMediaFileIds($mediaFileIds)
    {
      $this->mediaFileIds = $mediaFileIds;
      return $this;
    }

}
