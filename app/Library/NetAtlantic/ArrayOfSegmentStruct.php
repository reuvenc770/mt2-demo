<?php

namespace App\Library\NetAtlantic;

class ArrayOfSegmentStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var SegmentStruct[] $ArrayOfSegmentStruct
     */
    protected $ArrayOfSegmentStruct = null;

    /**
     * @param SegmentStruct[] $ArrayOfSegmentStruct
     */
    public function __construct(array $ArrayOfSegmentStruct)
    {
      $this->ArrayOfSegmentStruct = $ArrayOfSegmentStruct;
    }

    /**
     * @return SegmentStruct[]
     */
    public function getArrayOfSegmentStruct()
    {
      return $this->ArrayOfSegmentStruct;
    }

    /**
     * @param SegmentStruct[] $ArrayOfSegmentStruct
     * @return \App\Library\NetAtlantic\ArrayOfSegmentStruct
     */
    public function setArrayOfSegmentStruct(array $ArrayOfSegmentStruct)
    {
      $this->ArrayOfSegmentStruct = $ArrayOfSegmentStruct;
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
      return isset($this->ArrayOfSegmentStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return SegmentStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfSegmentStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param SegmentStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfSegmentStruct[] = $value;
      } else {
        $this->ArrayOfSegmentStruct[$offset] = $value;
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
      unset($this->ArrayOfSegmentStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return SegmentStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfSegmentStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfSegmentStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfSegmentStruct);
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
      reset($this->ArrayOfSegmentStruct);
    }

    /**
     * Countable implementation
     *
     * @return SegmentStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfSegmentStruct);
    }

}
