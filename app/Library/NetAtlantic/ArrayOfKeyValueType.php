<?php

namespace App\Library\NetAtlantic;

class ArrayOfKeyValueType implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var KeyValueType[] $ArrayOfKeyValueType
     */
    protected $ArrayOfKeyValueType = null;

    /**
     * @param KeyValueType[] $ArrayOfKeyValueType
     */
    public function __construct(array $ArrayOfKeyValueType)
    {
      $this->ArrayOfKeyValueType = $ArrayOfKeyValueType;
    }

    /**
     * @return KeyValueType[]
     */
    public function getArrayOfKeyValueType()
    {
      return $this->ArrayOfKeyValueType;
    }

    /**
     * @param KeyValueType[] $ArrayOfKeyValueType
     * @return \App\Library\NetAtlantic\ArrayOfKeyValueType
     */
    public function setArrayOfKeyValueType(array $ArrayOfKeyValueType)
    {
      $this->ArrayOfKeyValueType = $ArrayOfKeyValueType;
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
      return isset($this->ArrayOfKeyValueType[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return KeyValueType
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfKeyValueType[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param KeyValueType $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfKeyValueType[] = $value;
      } else {
        $this->ArrayOfKeyValueType[$offset] = $value;
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
      unset($this->ArrayOfKeyValueType[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return KeyValueType Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfKeyValueType);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfKeyValueType);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfKeyValueType);
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
      reset($this->ArrayOfKeyValueType);
    }

    /**
     * Countable implementation
     *
     * @return KeyValueType Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfKeyValueType);
    }

}
