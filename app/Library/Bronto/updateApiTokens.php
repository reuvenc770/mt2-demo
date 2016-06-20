<?php
namespace App\Library\Bronto;
class updateApiTokens
{

    /**
     * @var apiTokenObject[] $tokens
     */
    protected $tokens = null;

    /**
     * @param apiTokenObject[] $tokens
     */
    public function __construct(array $tokens)
    {
      $this->tokens = $tokens;
    }

    /**
     * @return apiTokenObject[]
     */
    public function getTokens()
    {
      return $this->tokens;
    }

    /**
     * @param apiTokenObject[] $tokens
     * @return updateApiTokens
     */
    public function setTokens(array $tokens)
    {
      $this->tokens = $tokens;
      return $this;
    }

}
