<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedRepo;

class FeedApiService {
    protected $repo;
    protected $feedRepo;

    protected $feedId = 0;
    protected $currentUrl = '';
    protected $referrerIp = '';

    public function __construct ( RawFeedEmailRepo $repo , FeedRepo $feedRepo ) {
        $this->repo = $repo;
        $this->feedRepo = $feedRepo;
    }

    public function setRequestInfo ( $feedId , $currentUrl , $referrerIp ) {
        $this->feedId = $feedId;
        $this->currentUrl = $currentUrl;
        $this->referrerIp = $referrerIp;
    }

    public function getFeedIdFromPassword ( $password ) {
        return $this->feedRepo->getFeedIdFromPassword( $password );
    } 

    public function ingest ( $record ) {
        try {
            $record = $this->normalizeFields( $record );

            if (2430 === (int)$record['feed_id'] || 2618 === (int)$record['feed_id']) {
                $record['feed_id'] = 2979;
            }

            $cleanRecord = $this->repo->cleanseRecord( $record );

            $this->repo->create( $cleanRecord );

        } catch ( \Exception $e ) {
            $this->repo->logRealtimeFailure(
                $e->getTrace() ,
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

        $record[ 'first_name' ] = ( isset( $record[ 'firstname' ] ) ? $record[ 'firstname' ] : '' );
        unset( $record[ 'firstname' ] ); 

        $record[ 'last_name' ] =  ( isset( $record[ 'lastname' ] ) ? $record[ 'lastname' ] : '' );
        unset( $record[ 'lastname' ] );

        $record[ 'dob' ] = ( isset( $record[ 'birth_date' ] ) ? $record[ 'birth_date' ] : '' );
        unset( $record[ 'birth_date' ] );

        return $record;
    }
}
