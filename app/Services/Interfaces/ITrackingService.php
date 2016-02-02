<?php

namespace App\Services\Interfaces;

interface ITrackingService
{
  public function retrieveTrackingApiStats();
  public function insertApiRawStats($data);
}