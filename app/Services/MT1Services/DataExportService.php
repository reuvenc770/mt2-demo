<?php

namespace App\Services\MT1Services;
use App\Services\API\MT1Api;
use App\Services\ServiceTraits\PaginateMT1;

class DataExportService
{
    use PaginateMT1;

    public function __construct(MT1Api $apiService)
    {
        $this->pageName  = "dataexports";
        $this->api = $apiService;
    }

    public function getType () {
        return 'dataexport';
    }
}