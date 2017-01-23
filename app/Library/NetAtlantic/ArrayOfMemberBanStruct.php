<?php

namespace App\Library\NetAtlantic;

class ArrayOfMemberBanStruct implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var MemberBanStruct[] $ArrayOfMemberBanStruct
     */
    protected $ArrayOfMemberBanStruct = null;

    /**
     * @param MemberBanStruct[] $ArrayOfMemberBanStruct
     */
    public function __construct(array $ArrayOfMemberBanStruct)
    {
      $this->ArrayOfMemberBanStruct = $ArrayOfMemberBanStruct;
    }

    /**
     * @return MemberBanStruct[]
     */
    public function getArrayOfMemberBanStruct()
    {
      return $this->ArrayOfMemberBanStruct;
    }

    /**
     * @param MemberBanStruct[] $ArrayOfMemberBanStruct
     * @return \App\Library\NetAtlantic\ArrayOfMemberBanStruct
     */
    public function setArrayOfMemberBanStruct(array $ArrayOfMemberBanStruct)
    {
      $this->ArrayOfMemberBanStruct = $ArrayOfMemberBanStruct;
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
      return isset($this->ArrayOfMemberBanStruct[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return MemberBanStruct
     */
    public function offsetGet($offset)
    {
      return $this->ArrayOfMemberBanStruct[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param MemberBanStruct $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ArrayOfMemberBanStruct[] = $value;
      } else {
        $this->ArrayOfMemberBanStruct[$offset] = $value;
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
      unset($this->ArrayOfMemberBanStruct[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return MemberBanStruct Return the current element
     */
    public function current()
    {
      return current($this->ArrayOfMemberBanStruct);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ArrayOfMemberBanStruct);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ArrayOfMemberBanStruct);
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
      reset($this->ArrayOfMemberBanStruct);
    }

    /**
     * Countable implementation
     *
     * @return MemberBanStruct Return count of elements
     */
    public function count()
    {
      return count($this->ArrayOfMemberBanStruct);
    }

}
