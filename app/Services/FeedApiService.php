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
        try {
            $record = $this->normalizeFields( $record );

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
                isset( $record[ 'email_address' ] ) ? $record[ 'email_address' ] : '' ,
                $this->feedId
            );

            return [ 'status' => false , 'messages' => [ 'Server Error' ] ];
        }

        return [ 'status' => true , 'messages' => [ 'Record Accepted' ] ];
    }

    protected function normalizeFields ( $record ) {
        unset( $record[ 'pw' ] );
        $record[ 'feed_id' ] = $this->feedId;

        $record[ 'email_address' ] = $record[ 'email' ];
        unset( $record[ 'email' ] );

        $record[ 'first_name' ] = $record[ 'firstname' ];
        unset( $record[ 'firstname' ] );

        $record[ 'last_name' ] = $record[ 'lastname' ];
        unset( $record[ 'lastname' ] );

        $record[ 'dob' ] = $record[ 'birth_date' ];
        unset( $record[ 'birth_date' ] );

        return $record;
    }
}
