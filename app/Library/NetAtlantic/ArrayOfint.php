<?php

namespace App\Library\NetAtlantic;

class ArrayOfint implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var int[] $ArrayOfint
     */
    protected $ArrayOfint = null;

    /**
     * @param int[] $ArrayOfint
     */
    public function __construct(array $ArrayOfint)
    {
      $this->ArrayOfint = $ArrayOfint;
    }

    /**
     * @return int[]
     */
    public function getArrayOfint()
    {
      return $this->ArrayOfint;
    }

    /**
     * @param int[] $ArrayOfint
     * @return \App\Library\NetAtlantic\ArrayOfint
     */
    public function setArrayOfint(array $ArrayOfint)
    {
      $this->ArrayOfint = $ArrayOfint;
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
      return isset($this->ArrayOfint[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return int
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfint[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param int $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfint[] = $value;
      } else {
        $this->ArrayOfint[$offset] = $value;
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
      unset($this->ArrayOfint[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return int Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfint);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfint);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfint);
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
      reset($this->ArrayOfint);
    }

    /**
     * Countable implementation
     *
     * @return int Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfint);
    }

}
