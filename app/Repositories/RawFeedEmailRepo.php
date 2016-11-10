<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\RawFeedEmail;
use App\Models\RawFeedEmailFailed;

class RawFeedEmailRepo {
    protected $rawEmail;
    protected $failed;

    protected $standardFields = [
        'feed_id' => 0 ,
        'email_address' => 0 ,
        'source_url' => 0 ,
        'capture_date' => 0 ,
        'ip' => 0 ,
        'first_name' => 0 ,
        'last_name' => 0 ,
        'address' => 0 ,
        'address2' => 0 ,
        'city' => 0 ,
        'state' => 0 ,
        'zip' => 0 ,
        'country' => 0 ,
        'gender' => 0 ,
        'phone' => 0 ,
        'dob' => 0
    ];

    public function __construct ( RawFeedEmail $rawEmail ) {
        $this->rawEmail = $rawEmail;
    }

    public function create ( $data ) {
        $rawEmailRecord = array_intersect_key( $data , $this->standardFields );
        
        $customFields = array_diff_key( $data , $this->standardFields );

        $rawEmailRecord[ 'other_fields' ] = json_encode( $customFields );

        $this->rawEmail->create( $rawEmailRecord );
    }

    public function cleanseRecord ( $record ) {
        $cleanRecord = [];

        foreach ( $record as $fieldName => $fieldValue ) {
            $currentValue = preg_replace( '/[^\w\@\.\-\'\/\s]/' , '' , $fieldValue );
            $currentValue = preg_replace( '/\s{2,}/' , '' , $currentValue );
            $currentValue = trim( $currentValue );
            $currentValue = addslashes( $currentValue );

            $cleanRecord[ $fieldName ] = $currentValue; 
        }

        return $cleanRecord;
    }

    static public function logFailure ( $errors , $fullUrl , $referrerIp , $feedId = 0 ) {
        RawFeedEmailFailed::create( [
            'errors' => json_encode( $errors ) ,
            'url' => $fullUrl ,
            'ip' => $referrerIp ,
            'feed_id' => $feedId
        ] );
    }
}
