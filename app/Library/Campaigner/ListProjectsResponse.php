<?php
namespace App\Library\Campaigner;
class ListProjectsResponse
{

    /**
     * @var ArrayOfProjectDescription $ListProjectsResult
     */
    protected $ListProjectsResult = null;

    /**
     * @param ArrayOfProjectDescription $ListProjectsResult
     */
    public function __construct($ListProjectsResult)
    {
      $this->ListProjectsResult = $ListProjectsResult;
    }

    /**
     * @return ArrayOfProjectDescription
     */
    public function getListProjectsResult()
    {
      return $this->ListProjectsResult;
    }

    /**
     * @param ArrayOfProjectDescription $ListProjectsResult
     * @return ListProjectsResponse
     */
    public function setListProjectsResult($ListProjectsResult)
    {
      $this->ListProjectsResult = $ListProjectsResult;
      return $this;
    }

}
