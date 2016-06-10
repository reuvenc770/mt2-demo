<?php
namespace App\Library\Bronto;
class activityFilter
{

    /**
     * @var \DateTime $start
     */
    protected $start = null;

    /**
     * @var int $size
     */
    protected $size = null;

    /**
     * @var string[] $types
     */
    protected $types = null;

    /**
     * @var readDirection $readDirection
     */
    protected $readDirection = null;

    /**
     * @param \DateTime $start
     * @param int $size
     * @param readDirection $readDirection
     */
    public function __construct(\DateTime $start, $size, $readDirection)
    {
      $this->start = $start->format(\DateTime::ATOM);
      $this->size = $size;
      $this->readDirection = $readDirection;
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
     * @return activityFilter
     */
    public function setStart(\DateTime $start)
    {
      $this->start = $start->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
      return $this->size;
    }

    /**
     * @param int $size
     * @return activityFilter
     */
    public function setSize($size)
    {
      $this->size = $size;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
      return $this->types;
    }

    /**
     * @param string[] $types
     * @return activityFilter
     */
    public function setTypes(array $types)
    {
      $this->types = $types;
      return $this;
    }

    /**
     * @return readDirection
     */
    public function getReadDirection()
    {
      return $this->readDirection;
    }

    /**
     * @param readDirection $readDirection
     * @return activityFilter
     */
    public function setReadDirection($readDirection)
    {
      $this->readDirection = $readDirection;
      return $this;
    }

}
