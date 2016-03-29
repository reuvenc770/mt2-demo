<?php

namespace App\Services\Interfaces;

interface IDataService
{
  public function retrieveApiStats($data);
  public function insertApiRawStats($data);
  public function mapToStandardReport($data);
  public function insertSegmentedApiRawStats($data, $length);
  public function insertCsvRawStats($data);
}