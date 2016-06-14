<?php
namespace App\Library\Bronto;
class writeResult
{

    /**
     * @var int[] $errors
     */
    protected $errors = null;

    /**
     * @var resultItem[] $results
     */
    protected $results = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int[]
     */
    public function getErrors()
    {
      return $this->errors;
    }

    /**
     * @param int[] $errors
     * @return writeResult
     */
    public function setErrors(array $errors)
    {
      $this->errors = $errors;
      return $this;
    }

    /**
     * @return resultItem[]
     */
    public function getResults()
    {
      return $this->results;
    }

    /**
     * @param resultItem[] $results
     * @return writeResult
     */
    public function setResults(array $results)
    {
      $this->results = $results;
      return $this;
    }

}
