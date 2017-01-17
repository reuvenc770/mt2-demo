<?php

namespace App\Library\NetAtlantic;

class ArrayOfSimpleMemberStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var SimpleMemberStruct[] $ArrayOfSimpleMemberStruct
     */
    protected $ArrayOfSimpleMemberStruct = null;

    /**
     * @param SimpleMemberStruct[] $ArrayOfSimpleMemberStruct
     */
    public function __construct(array $ArrayOfSimpleMemberStruct)
    {
      $this->ArrayOfSimpleMemberStruct = $ArrayOfSimpleMemberStruct;
    }

    /**
     * @return SimpleMemberStruct[]
     */
    public function getArrayOfSimpleMemberStruct()
    {
      return $this->ArrayOfSimpleMemberStruct;
    }

    /**
     * @param SimpleMemberStruct[] $ArrayOfSimpleMemberStruct
     * @return \App\Library\NetAtlantic\ArrayOfSimpleMemberStruct
     */
    public function setArrayOfSimpleMemberStruct(array $ArrayOfSimpleMemberStruct)
    {
      $this->ArrayOfSimpleMemberStruct = $ArrayOfSimpleMemberStruct;
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
      return isset($this->ArrayOfSimpleMemberStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return SimpleMemberStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfSimpleMemberStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param SimpleMemberStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfSimpleMemberStruct[] = $value;
      } else {
        $this->ArrayOfSimpleMemberStruct[$offset] = $value;
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
      unset($this->ArrayOfSimpleMemberStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return SimpleMemberStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfSimpleMemberStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfSimpleMemberStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfSimpleMemberStruct);
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
      reset($this->ArrayOfSimpleMemberStruct);
    }

    /**
     * Countable implementation
     *
     * @return SimpleMemberStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfSimpleMemberStruct);
    }

}
