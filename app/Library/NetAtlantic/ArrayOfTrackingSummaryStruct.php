<?php

namespace App\Library\NetAtlantic;

class ArrayOfTrackingSummaryStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var TrackingSummaryStruct[] $ArrayOfTrackingSummaryStruct
     */
    protected $ArrayOfTrackingSummaryStruct = null;

    /**
     * @param TrackingSummaryStruct[] $ArrayOfTrackingSummaryStruct
     */
    public function __construct(array $ArrayOfTrackingSummaryStruct)
    {
      $this->ArrayOfTrackingSummaryStruct = $ArrayOfTrackingSummaryStruct;
    }

    /**
     * @return TrackingSummaryStruct[]
     */
    public function getArrayOfTrackingSummaryStruct()
    {
      return $this->ArrayOfTrackingSummaryStruct;
    }

    /**
     * @param TrackingSummaryStruct[] $ArrayOfTrackingSummaryStruct
     * @return \App\Library\NetAtlantic\ArrayOfTrackingSummaryStruct
     */
    public function setArrayOfTrackingSummaryStruct(array $ArrayOfTrackingSummaryStruct)
    {
      $this->ArrayOfTrackingSummaryStruct = $ArrayOfTrackingSummaryStruct;
      return $this;
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
      return isset($this->ArrayOfTrackingSummaryStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return TrackingSummaryStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfTrackingSummaryStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param TrackingSummaryStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfTrackingSummaryStruct[] = $value;
      } else {
        $this->ArrayOfTrackingSummaryStruct[$offset] = $value;
      }
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
      unset($this->ArrayOfTrackingSummaryStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return TrackingSummaryStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfTrackingSummaryStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfTrackingSummaryStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfTrackingSummaryStruct);
    }

    /**
     * Iterator implementation
     *
     * @return boolean Return the validity of the current position
     */
    public function valid()
    {
      return $this->key() !== null;
    }

    /**
     * Iterator implementation
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
      reset($this->ArrayOfTrackingSummaryStruct);
    }

    /**
     * Countable implementation
     *
     * @return TrackingSummaryStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfTrackingSummaryStruct);
    }

}
