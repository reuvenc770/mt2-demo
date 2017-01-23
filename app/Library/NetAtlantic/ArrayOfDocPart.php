<?php

namespace App\Library\NetAtlantic;

class ArrayOfDocPart implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var DocPart[] $ArrayOfDocPart
     */
    protected $ArrayOfDocPart = null;

    /**
     * @param DocPart[] $ArrayOfDocPart
     */
    public function __construct(array $ArrayOfDocPart)
    {
      $this->ArrayOfDocPart = $ArrayOfDocPart;
    }

    /**
     * @return DocPart[]
     */
    public function getArrayOfDocPart()
    {
      return $this->ArrayOfDocPart;
    }

    /**
     * @param DocPart[] $ArrayOfDocPart
     * @return \App\Library\NetAtlantic\ArrayOfDocPart
     */
    public function setArrayOfDocPart(array $ArrayOfDocPart)
    {
      $this->ArrayOfDocPart = $ArrayOfDocPart;
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
      return isset($this->ArrayOfDocPart[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return DocPart
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfDocPart[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param DocPart $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfDocPart[] = $value;
      } else {
        $this->ArrayOfDocPart[$offset] = $value;
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
      unset($this->ArrayOfDocPart[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return DocPart Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfDocPart);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfDocPart);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfDocPart);
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
      reset($this->ArrayOfDocPart);
    }

    /**
     * Countable implementation
     *
     * @return DocPart Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfDocPart);
    }

}
