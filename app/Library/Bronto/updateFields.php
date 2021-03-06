<?php
namespace App\Library\Bronto;
class updateFields
{

    /**
     * @var fieldObject[] $fields
     */
    protected $fields = null;

    /**
     * @param fieldObject[] $fields
     */
    public function __construct(array $fields)
    {
      $this->fields = $fields;
    }

    /**
     * @return fieldObject[]
     */
    public function getFields()
    {
      return $this->fields;
    }

    /**
     * @param fieldObject[] $fields
     * @return updateFields
     */
    public function setFields(array $fields)
    {
      $this->fields = $fields;
      return $this;
    }

}
