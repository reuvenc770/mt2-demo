<?php

namespace App\Library\NetAtlantic;

class ArrayOfMailingStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var MailingStruct[] $ArrayOfMailingStruct
     */
    protected $ArrayOfMailingStruct = null;

    /**
     * @param MailingStruct[] $ArrayOfMailingStruct
     */
    public function __construct(array $ArrayOfMailingStruct)
    {
      $this->ArrayOfMailingStruct = $ArrayOfMailingStruct;
    }

    /**
     * @return MailingStruct[]
     */
    public function getArrayOfMailingStruct()
    {
      return $this->ArrayOfMailingStruct;
    }

    /**
     * @param MailingStruct[] $ArrayOfMailingStruct
     * @return \App\Library\NetAtlantic\ArrayOfMailingStruct
     */
    public function setArrayOfMailingStruct(array $ArrayOfMailingStruct)
    {
      $this->ArrayOfMailingStruct = $ArrayOfMailingStruct;
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
      return isset($this->ArrayOfMailingStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return MailingStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfMailingStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param MailingStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfMailingStruct[] = $value;
      } else {
        $this->ArrayOfMailingStruct[$offset] = $value;
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
      unset($this->ArrayOfMailingStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return MailingStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfMailingStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfMailingStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfMailingStruct);
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
      reset($this->ArrayOfMailingStruct);
    }

    /**
     * Countable implementation
     *
     * @return MailingStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfMailingStruct);
    }

}
