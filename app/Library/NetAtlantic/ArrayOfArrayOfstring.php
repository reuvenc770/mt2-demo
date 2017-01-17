<?php

namespace App\Library\NetAtlantic;

class ArrayOfArrayOfstring implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ArrayOfstring[] $ArrayOfArrayOfstring
     */
    protected $ArrayOfArrayOfstring = null;

    /**
     * @param ArrayOfstring[] $ArrayOfArrayOfstring
     */
    public function __construct(array $ArrayOfArrayOfstring)
    {
      $this->ArrayOfArrayOfstring = $ArrayOfArrayOfstring;
    }

    /**
     * @return ArrayOfstring[]
     */
    public function getArrayOfArrayOfstring()
    {
      return $this->ArrayOfArrayOfstring;
    }

    /**
     * @param ArrayOfstring[] $ArrayOfArrayOfstring
     * @return \App\Library\NetAtlantic\ArrayOfArrayOfstring
     */
    public function setArrayOfArrayOfstring(array $ArrayOfArrayOfstring)
    {
      $this->ArrayOfArrayOfstring = $ArrayOfArrayOfstring;
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
      return isset($this->ArrayOfArrayOfstring[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ArrayOfstring
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfArrayOfstring[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ArrayOfstring $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfArrayOfstring[] = $value;
      } else {
        $this->ArrayOfArrayOfstring[$offset] = $value;
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
      unset($this->ArrayOfArrayOfstring[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ArrayOfstring Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfArrayOfstring);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfArrayOfstring);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfArrayOfstring);
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
      reset($this->ArrayOfArrayOfstring);
    }

    /**
     * Countable implementation
     *
     * @return ArrayOfstring Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfArrayOfstring);
    }

}
