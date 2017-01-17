<?php

namespace App\Library\NetAtlantic;

class ArrayOfSimpleMailingStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var SimpleMailingStruct[] $ArrayOfSimpleMailingStruct
     */
    protected $ArrayOfSimpleMailingStruct = null;

    /**
     * @param SimpleMailingStruct[] $ArrayOfSimpleMailingStruct
     */
    public function __construct(array $ArrayOfSimpleMailingStruct)
    {
      $this->ArrayOfSimpleMailingStruct = $ArrayOfSimpleMailingStruct;
    }

    /**
     * @return SimpleMailingStruct[]
     */
    public function getArrayOfSimpleMailingStruct()
    {
      return $this->ArrayOfSimpleMailingStruct;
    }

    /**
     * @param SimpleMailingStruct[] $ArrayOfSimpleMailingStruct
     * @return \App\Library\NetAtlantic\ArrayOfSimpleMailingStruct
     */
    public function setArrayOfSimpleMailingStruct(array $ArrayOfSimpleMailingStruct)
    {
      $this->ArrayOfSimpleMailingStruct = $ArrayOfSimpleMailingStruct;
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
      return isset($this->ArrayOfSimpleMailingStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return SimpleMailingStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfSimpleMailingStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param SimpleMailingStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfSimpleMailingStruct[] = $value;
      } else {
        $this->ArrayOfSimpleMailingStruct[$offset] = $value;
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
      unset($this->ArrayOfSimpleMailingStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return SimpleMailingStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfSimpleMailingStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfSimpleMailingStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfSimpleMailingStruct);
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
      reset($this->ArrayOfSimpleMailingStruct);
    }

    /**
     * Countable implementation
     *
     * @return SimpleMailingStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfSimpleMailingStruct);
    }

}
