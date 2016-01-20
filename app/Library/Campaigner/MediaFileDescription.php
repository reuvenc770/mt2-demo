<?php
namespace App\Library\Campaigner;
class MediaFileDescription
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $LogicalFileName
     */
    protected $LogicalFileName = null;

    /**
     * @var string $PhysicalFileName
     */
    protected $PhysicalFileName = null;

    /**
     * @var MediaFileType $FileType
     */
    protected $FileType = null;

    /**
     * @var int $FileSize
     */
    protected $FileSize = null;

    /**
     * @var string $FileURL
     */
    protected $FileURL = null;

    /**
     * @param int $Id
     * @param string $LogicalFileName
     * @param string $PhysicalFileName
     * @param MediaFileType $FileType
     * @param int $FileSize
     * @param string $FileURL
     */
    public function __construct($Id, $LogicalFileName, $PhysicalFileName, $FileType, $FileSize, $FileURL)
    {
      $this->Id = $Id;
      $this->LogicalFileName = $LogicalFileName;
      $this->PhysicalFileName = $PhysicalFileName;
      $this->FileType = $FileType;
      $this->FileSize = $FileSize;
      $this->FileURL = $FileURL;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return MediaFileDescription
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getLogicalFileName()
    {
      return $this->LogicalFileName;
    }

    /**
     * @param string $LogicalFileName
     * @return MediaFileDescription
     */
    public function setLogicalFileName($LogicalFileName)
    {
      $this->LogicalFileName = $LogicalFileName;
      return $this;
    }

    /**
     * @return string
     */
    public function getPhysicalFileName()
    {
      return $this->PhysicalFileName;
    }

    /**
     * @param string $PhysicalFileName
     * @return MediaFileDescription
     */
    public function setPhysicalFileName($PhysicalFileName)
    {
      $this->PhysicalFileName = $PhysicalFileName;
      return $this;
    }

    /**
     * @return MediaFileType
     */
    public function getFileType()
    {
      return $this->FileType;
    }

    /**
     * @param MediaFileType $FileType
     * @return MediaFileDescription
     */
    public function setFileType($FileType)
    {
      $this->FileType = $FileType;
      return $this;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
      return $this->FileSize;
    }

    /**
     * @param int $FileSize
     * @return MediaFileDescription
     */
    public function setFileSize($FileSize)
    {
      $this->FileSize = $FileSize;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileURL()
    {
      return $this->FileURL;
    }

    /**
     * @param string $FileURL
     * @return MediaFileDescription
     */
    public function setFileURL($FileURL)
    {
      $this->FileURL = $FileURL;
      return $this;
    }

}
