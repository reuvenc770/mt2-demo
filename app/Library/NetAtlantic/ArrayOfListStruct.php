<?php

namespace App\Library\NetAtlantic;

class ArrayOfListStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ListStruct[] $ArrayOfListStruct
     */
    protected $ArrayOfListStruct = null;

    /**
     * @param ListStruct[] $ArrayOfListStruct
     */
    public function __construct(array $ArrayOfListStruct)
    {
      $this->ArrayOfListStruct = $ArrayOfListStruct;
    }

    /**
     * @return ListStruct[]
     */
    public function getArrayOfListStruct()
    {
      return $this->ArrayOfListStruct;
    }

    /**
     * @param ListStruct[] $ArrayOfListStruct
     * @return \App\Library\NetAtlantic\ArrayOfListStruct
     */
    public function setArrayOfListStruct(array $ArrayOfListStruct)
    {
      $this->ArrayOfListStruct = $ArrayOfListStruct;
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
      return isset($this->ArrayOfListStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ListStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfListStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ListStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfListStruct[] = $value;
      } else {
        $this->ArrayOfListStruct[$offset] = $value;
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
      unset($this->ArrayOfListStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ListStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfListStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfListStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfListStruct);
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
      reset($this->ArrayOfListStruct);
    }

    /**
     * Countable implementation
     *
     * @return ListStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfListStruct);
    }

}
