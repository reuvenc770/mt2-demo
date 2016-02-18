<?php
namespace App\Library\Campaigner;
class UploadMediaFile
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $fileName
     */
    protected $fileName = null;

    /**
     * @var string $fileContentBase64
     */
    protected $fileContentBase64 = null;

    /**
     * @param Authentication $authentication
     * @param string $fileName
     * @param string $fileContentBase64
     */
    public function __construct($authentication, $fileName, $fileContentBase64)
    {
      $this->authentication = $authentication;
      $this->fileName = $fileName;
      $this->fileContentBase64 = $fileContentBase64;
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
     * @return UploadMediaFile
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
      return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return UploadMediaFile
     */
    public function setFileName($fileName)
    {
      $this->fileName = $fileName;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileContentBase64()
    {
      return $this->fileContentBase64;
    }

    /**
     * @param string $fileContentBase64
     * @return UploadMediaFile
     */
    public function setFileContentBase64($fileContentBase64)
    {
      $this->fileContentBase64 = $fileContentBase64;
      return $this;
    }

}
