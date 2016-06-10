<?php

class contactField
{

    /**
     * @var string $fieldId
     */
    protected $fieldId = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @param string $fieldId
     * @param string $content
     */
    public function __construct($fieldId, $content)
    {
      $this->fieldId = $fieldId;
      $this->content = $content;
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
      return $this->fieldId;
    }

    /**
     * @param string $fieldId
     * @return contactField
     */
    public function setFieldId($fieldId)
    {
      $this->fieldId = $fieldId;
      return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param string $content
     * @return contactField
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

}
