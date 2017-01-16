<?php

namespace App\Library\NetAtlantic;

class ArrayOfMemberStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var MemberStruct[] $ArrayOfMemberStruct
     */
    protected $ArrayOfMemberStruct = null;

    /**
     * @param MemberStruct[] $ArrayOfMemberStruct
     */
    public function __construct(array $ArrayOfMemberStruct)
    {
      $this->ArrayOfMemberStruct = $ArrayOfMemberStruct;
    }

    /**
     * @return MemberStruct[]
     */
    public function getArrayOfMemberStruct()
    {
      return $this->ArrayOfMemberStruct;
    }

    /**
     * @param MemberStruct[] $ArrayOfMemberStruct
     * @return \App\Library\NetAtlantic\ArrayOfMemberStruct
     */
    public function setArrayOfMemberStruct(array $ArrayOfMemberStruct)
    {
      $this->ArrayOfMemberStruct = $ArrayOfMemberStruct;
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
      return isset($this->ArrayOfMemberStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return MemberStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfMemberStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param MemberStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfMemberStruct[] = $value;
      } else {
        $this->ArrayOfMemberStruct[$offset] = $value;
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
      unset($this->ArrayOfMemberStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return MemberStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfMemberStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfMemberStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfMemberStruct);
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
      reset($this->ArrayOfMemberStruct);
    }

    /**
     * Countable implementation
     *
     * @return MemberStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfMemberStruct);
    }

}
