<?php

namespace App\Library\NetAtlantic;

class ArrayOfCharSetStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var CharSetStruct[] $ArrayOfCharSetStruct
     */
    protected $ArrayOfCharSetStruct = null;

    /**
     * @param CharSetStruct[] $ArrayOfCharSetStruct
     */
    public function __construct(array $ArrayOfCharSetStruct)
    {
      $this->ArrayOfCharSetStruct = $ArrayOfCharSetStruct;
    }

    /**
     * @return CharSetStruct[]
     */
    public function getArrayOfCharSetStruct()
    {
      return $this->ArrayOfCharSetStruct;
    }

    /**
     * @param CharSetStruct[] $ArrayOfCharSetStruct
     * @return \App\Library\NetAtlantic\ArrayOfCharSetStruct
     */
    public function setArrayOfCharSetStruct(array $ArrayOfCharSetStruct)
    {
      $this->ArrayOfCharSetStruct = $ArrayOfCharSetStruct;
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
      return isset($this->ArrayOfCharSetStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return CharSetStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfCharSetStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param CharSetStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfCharSetStruct[] = $value;
      } else {
        $this->ArrayOfCharSetStruct[$offset] = $value;
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
      unset($this->ArrayOfCharSetStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return CharSetStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfCharSetStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfCharSetStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfCharSetStruct);
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
      reset($this->ArrayOfCharSetStruct);
    }

    /**
     * Countable implementation
     *
     * @return CharSetStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfCharSetStruct);
    }

}
