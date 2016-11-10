<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedRepo;

class FeedApiService {
    protected $repo;

    protected $feedId = 0;
    protected $currentUrl = '';
    protected $referrerIp = '';

    public function __construct ( RawFeedEmailRepo $repo ) {
        $this->repo = $repo;
    }

    public function setRequestInfo ( $feedId , $currentUrl , $referrerIp ) {
        $this->feedId = $feedId;
        $this->currentUrl = $currentUrl;
        $this->referrerIp = $referrerIp;
    }

    public function getFeedIdFromPassword ( $password ) {
        return FeedRepo::getFeedIdFromPassword( $password );
    } 

    public function ingest ( $record ) {
        unset( $record[ 'pw' ] );
        $record[ 'feed_id' ] = $this->feedId;

        try {
            $cleanRecord = $this->repo->cleanseRecord( $record );

            $this->repo->create( $cleanRecord );
        } catch ( \Exception $e ) {
            $this->repo->logFailure(
                [
                    'message' => $e->getMessage() ,
                    'file' => $e->getFile() ,
                    'line' => $e->getLine() ,
                    'trace' => $e->getTraceAsString()
                ] ,
                $this->currentUrl ,
                $this->referrerIp ,
                $this->feedId
            );

            return [ 'status' => false , 'messages' => [ 'Server Error' ] ];
        }

        return [ 'status' => true , 'messages' => [ 'Record Accepted' ] ];
    }
}
