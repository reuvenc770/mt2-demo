<?php

namespace App\Services;

use App\Repositories\ProcessedFeedFileRepo;

class ProcessedFeedFileService {
    protected $repo;

    public function __construct ( ProcessedFeedFileRepo $repo ) {
        $this->repo = $repo;
    }

    public function fileWasProcessed ( $path ) {
        return $this->repo->fileWasProcessed( $path );
    }

    public function fileLineCountMatches ( $path , $count ) {
        return $this->repo->fileLineCountMatches( $path , $count );
    }

    public function getProcessedTime ( $path ) {
        return $this->repo->getProcessedTime( $path );
    }
}
