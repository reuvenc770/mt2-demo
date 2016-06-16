<?php
namespace App\Library\Bronto;
class bounceFilter
{

    /**
     * @var string $contactId
     */
    protected $contactId = null;

    /**
     * @var \DateTime $start
     */
    protected $start = null;

    /**
     * @var \DateTime $end
     */
    protected $end = null;

    /**
     * @param string $contactId
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct($contactId, \DateTime $start, \DateTime $end)
    {
      $this->contactId = $contactId;
      $this->start = $start->format(\DateTime::ATOM);
      $this->end = $end->format(\DateTime::ATOM);
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
     * @return bounceFilter
     */
    public function setContactId($contactId)
    {
      $this->contactId = $contactId;
      return $this;
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
     * @return bounceFilter
     */
    public function setStart(\DateTime $start)
    {
      $this->start = $start->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
      if ($this->end == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->end);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $end
     * @return bounceFilter
     */
    public function setEnd(\DateTime $end)
    {
      $this->end = $end->format(\DateTime::ATOM);
      return $this;
    }

}
