<?php

namespace App\Library\NetAtlantic;

class ArrayOfContentStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ContentStruct[] $ArrayOfContentStruct
     */
    protected $ArrayOfContentStruct = null;

    /**
     * @param ContentStruct[] $ArrayOfContentStruct
     */
    public function __construct(array $ArrayOfContentStruct)
    {
      $this->ArrayOfContentStruct = $ArrayOfContentStruct;
    }

    /**
     * @return ContentStruct[]
     */
    public function getArrayOfContentStruct()
    {
      return $this->ArrayOfContentStruct;
    }

    /**
     * @param ContentStruct[] $ArrayOfContentStruct
     * @return \App\Library\NetAtlantic\ArrayOfContentStruct
     */
    public function setArrayOfContentStruct(array $ArrayOfContentStruct)
    {
      $this->ArrayOfContentStruct = $ArrayOfContentStruct;
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
      return isset($this->ArrayOfContentStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ContentStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfContentStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ContentStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfContentStruct[] = $value;
      } else {
        $this->ArrayOfContentStruct[$offset] = $value;
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
      unset($this->ArrayOfContentStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ContentStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfContentStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfContentStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfContentStruct);
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
      reset($this->ArrayOfContentStruct);
    }

    /**
     * Countable implementation
     *
     * @return ContentStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfContentStruct);
    }

}
