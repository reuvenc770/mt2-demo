<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\RawFeedEmail;
use App\Models\RawFeedEmailFailed;

use Carbon\Carbon;

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

    public function __construct ( RawFeedEmail $rawEmail , RawFeedEmailFailed $failed ) {
        $this->rawEmail = $rawEmail;
        $this->failed = $failed;
    }

    public function create ( $data ) {
        $this->rawEmail->create( $this->fixRecordStructure( $data ) );
    }

    public function massInsert ( $recordStringList ) {
        $recordSqlString = implode( ' , ' , $recordStringList );

        \DB::insert( "
            INSERT INTO
                raw_feed_emails (
                    feed_id ,
                    email_address ,
                    source_url ,
                    capture_date ,
                    ip ,
                    first_name ,
                    last_name ,
                    address ,
                    address2 ,
                    city ,
                    state ,
                    zip ,
                    country ,
                    gender ,
                    phone ,
                    dob ,
                    other_fields ,
                    created_at ,
                    updated_at
                )
            VALUES
                {$recordSqlString}    
        " );
    }

    public function toSqlFormat ( $record ) {
        $cleanRecord = $this->cleanseRecord( $record );

        $finalRecord = $this->fixRecordStructure( $cleanRecord );

        return $this->formatRecord( $finalRecord );
    }

    public function cleanseRecord ( $record ) {
        $cleanRecord = [];

        foreach ( $record as $fieldName => $fieldValue ) {
            $currentValue = preg_replace( '/[^\w\@\.\-\'\/\s]/' , '' , $fieldValue );
            $currentValue = preg_replace( '/\s{2,}/' , '' , $currentValue );
            $currentValue = trim( $currentValue );

            $cleanRecord[ $fieldName ] = $currentValue; 
        }

        return $cleanRecord;
    }

    public function logFailure ( $errors , $fullUrl , $referrerIp , $email = '' , $feedId = 0 ) {
        $this->failed->create( [
            'errors' => json_encode( $errors ) ,
            'url' => $fullUrl ,
            'ip' => $referrerIp ,
            'email' => $email ,
            'feed_id' => $feedId
        ] );
    }

    public function getFirstPartyRecordsFromFeed($startPoint, $feedId) {
        return $this->rawEmail
                    ->selectRaw("raw_feed_emails.*, email_domain_id, domain_group_id, e.id as email_id")
                    ->leftJoin('emails as e', 'raw_feed_emails.email_address', '=', 'e.email_address')
                    ->leftJoin('email_domains as ed', 'e.email_domain_id', '=', 'ed.id')
                    ->where('feed_id', $feedId)
                    ->where('raw_feed_emails.id', '>', $startPoint)
                    ->orderBy('raw_feed_emails.id')
                    ->limit(1000)
                    ->get();
    }

    public function getThirdPartyRecordsWithChars($startPoint, $startChars) {
        $charsRegex = '^[' . $startChars . ']';
        $suppDb = config('database.connections.suppression.database');

        return $this->rawEmail
                    ->selectRaw("raw_feed_emails.*, email_domain_id, domain_group_id, e.id as email_id, IF(sgo.id IS NULL, 0, 1) as suppressed")
                    ->leftJoin('emails as e', 'raw_feed_emails.email_address', '=', 'e.email_address')
                    ->leftJoin('email_domains as ed', 'e.email_domain_id', '=', 'ed.id')
                    ->leftJoin("$suppDb.suppression_global_orange as sgo", 'raw_feed_emails.email_address', '=', 'sgo.email_address')
                    ->whereRaw("raw_feed_emails.email_address RLIKE '$charsRegex'")
                    ->where('raw_feed_emails.id', '>', $startPoint)
                    ->orderBy('raw_feed_emails.id')
                    ->limit(1000)
                    ->get();
    }

    protected function formatRecord ( $record ) {
        $pdo = \DB::connection()->getPdo();

        try {
            $captureDate = Carbon::parse( $record[ 'capture_date' ] )->toDateTimeString();
        } catch ( \Exception $e ) {
            \Log::error( $e );

            $captureDate = $record[ 'capture_date' ];
        }

        try {
            $dob = Carbon::parse( $record[ 'dob' ] )->toDateString();
        } catch ( \Exception $e ) {
            \Log::error( $e );

            $dob = $record[ 'dob' ];
        }

        return "("
            . $pdo->quote( $record[ 'feed_id' ] ) . ","
            . $pdo->quote( $record[ 'email_address' ] ) . ","
            . $pdo->quote( $record[ 'source_url' ] ) . ","
            . $pdo->quote( $captureDate ) . ","
            . $pdo->quote( $record[ 'ip' ] ) . ","
            . ( isset( $record[ 'first_name' ] ) ? $pdo->quote( $record[ 'first_name' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'last_name' ] ) ? $pdo->quote( $record[ 'last_name' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'address' ] ) ? $pdo->quote( $record[ 'address' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'address2' ] ) ? $pdo->quote( $record[ 'address2' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'city' ] ) ? $pdo->quote( $record[ 'city' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'state' ] ) ? $pdo->quote( $record[ 'state' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'zip' ] ) ? $pdo->quote( $record[ 'zip' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'country' ] ) ? $pdo->quote( $record[ 'country' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'gender' ] ) ? $pdo->quote( $record[ 'gender' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'phone' ] ) ? $pdo->quote( $record[ 'phone' ] ) : 'NULL' ) . ","
            . ( isset( $dob ) ? $pdo->quote( $dob ) : 'NULL' ) . ","
            . ( isset( $record[ 'other_fields' ] ) ? $pdo->quote( $record[ 'other_fields' ] ) : '{}' ) . ","
            . "NOW() ,"
            . "NOW()"
        . ")";
    }

    protected function fixRecordStructure ( $record ) {
        $rawEmailRecord = array_intersect_key( $record , $this->standardFields );
        
        $customFields = array_diff_key( $record , $this->standardFields );

        $rawEmailRecord[ 'other_fields' ] = json_encode( $customFields );

        return $rawEmailRecord;
    }
}
