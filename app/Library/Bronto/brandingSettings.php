<?php
namespace App\Library\Bronto;
class brandingSettings
{

    /**
     * @var string $brandingImage
     */
    protected $brandingImage = null;

    /**
     * @var string $brandingImageLink
     */
    protected $brandingImageLink = null;

    /**
     * @var string $brandingImageUrl
     */
    protected $brandingImageUrl = null;

    /**
     * @param string $brandingImage
     * @param string $brandingImageLink
     * @param string $brandingImageUrl
     */
    public function __construct($brandingImage, $brandingImageLink, $brandingImageUrl)
    {
      $this->brandingImage = $brandingImage;
      $this->brandingImageLink = $brandingImageLink;
      $this->brandingImageUrl = $brandingImageUrl;
    }

    /**
     * @return string
     */
    public function getBrandingImage()
    {
      return $this->brandingImage;
    }

    /**
     * @param string $brandingImage
     * @return brandingSettings
     */
    public function setBrandingImage($brandingImage)
    {
      $this->brandingImage = $brandingImage;
      return $this;
    }

    /**
     * @return string
     */
    public function getBrandingImageLink()
    {
      return $this->brandingImageLink;
    }

    /**
     * @param string $brandingImageLink
     * @return brandingSettings
     */
    public function setBrandingImageLink($brandingImageLink)
    {
      $this->brandingImageLink = $brandingImageLink;
      return $this;
    }

    /**
     * @return string
     */
    public function getBrandingImageUrl()
    {
      return $this->brandingImageUrl;
    }

    /**
     * @param string $brandingImageUrl
     * @return brandingSettings
     */
    public function setBrandingImageUrl($brandingImageUrl)
    {
      $this->brandingImageUrl = $brandingImageUrl;
      return $this;
    }

}
