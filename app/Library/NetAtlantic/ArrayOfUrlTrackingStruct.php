<?php

namespace App\Library\NetAtlantic;

class ArrayOfUrlTrackingStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var UrlTrackingStruct[] $ArrayOfUrlTrackingStruct
     */
    protected $ArrayOfUrlTrackingStruct = null;

    /**
     * @param UrlTrackingStruct[] $ArrayOfUrlTrackingStruct
     */
    public function __construct(array $ArrayOfUrlTrackingStruct)
    {
      $this->ArrayOfUrlTrackingStruct = $ArrayOfUrlTrackingStruct;
    }

    /**
     * @return UrlTrackingStruct[]
     */
    public function getArrayOfUrlTrackingStruct()
    {
      return $this->ArrayOfUrlTrackingStruct;
    }

    /**
     * @param UrlTrackingStruct[] $ArrayOfUrlTrackingStruct
     * @return \App\Library\NetAtlantic\ArrayOfUrlTrackingStruct
     */
    public function setArrayOfUrlTrackingStruct(array $ArrayOfUrlTrackingStruct)
    {
      $this->ArrayOfUrlTrackingStruct = $ArrayOfUrlTrackingStruct;
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
      return isset($this->ArrayOfUrlTrackingStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return UrlTrackingStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfUrlTrackingStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param UrlTrackingStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfUrlTrackingStruct[] = $value;
      } else {
        $this->ArrayOfUrlTrackingStruct[$offset] = $value;
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
      unset($this->ArrayOfUrlTrackingStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return UrlTrackingStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfUrlTrackingStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfUrlTrackingStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfUrlTrackingStruct);
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
      reset($this->ArrayOfUrlTrackingStruct);
    }

    /**
     * Countable implementation
     *
     * @return UrlTrackingStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfUrlTrackingStruct);
    }

}
