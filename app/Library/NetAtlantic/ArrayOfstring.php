<?php

namespace App\Library\NetAtlantic;

class ArrayOfstring implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var string[] $ArrayOfstring
     */
    protected $ArrayOfstring = null;

    /**
     * @param string[] $ArrayOfstring
     */
    public function __construct(array $ArrayOfstring)
    {
      $this->ArrayOfstring = $ArrayOfstring;
    }

    /**
     * @return string[]
     */
    public function getArrayOfstring()
    {
      return $this->ArrayOfstring;
    }

    /**
     * @param string[] $ArrayOfstring
     * @return \App\Library\NetAtlantic\ArrayOfstring
     */
    public function setArrayOfstring(array $ArrayOfstring)
    {
      $this->ArrayOfstring = $ArrayOfstring;
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
      return isset($this->ArrayOfstring[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return string
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfstring[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param string $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfstring[] = $value;
      } else {
        $this->ArrayOfstring[$offset] = $value;
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
      unset($this->ArrayOfstring[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return string Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfstring);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfstring);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfstring);
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
      reset($this->ArrayOfstring);
    }

    /**
     * Countable implementation
     *
     * @return string Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfstring);
    }

}
