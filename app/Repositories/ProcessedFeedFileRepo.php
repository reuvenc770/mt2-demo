<?php

namespace App\Repositories;

use App\Models\ProcessedFeedFile;

class ProcessedFeedFileRepo {
    protected $file;

    public function __construct ( ProcessedFeedFile $file ) {
        $this->file = $file;
    }

    public function fileWasProcessed ( $path ) {
        return is_null( $this->file->find( $path ) ) ? false : true;
    }

    public function fileLineCountMatches ( $path , $count ) {
        return $this->file->where( [ [ 'line_count' , '=' , $count ] , [ 'path' , '=' , $path ] ] )->count() ? true : false;
    }

    public function getProcessedTime ( $path ) {
        return is_null( $result = $this->file->find( $path ) ) ? null : $result->created_at;
    }
}
