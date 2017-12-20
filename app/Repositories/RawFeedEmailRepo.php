<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\RawFeedEmail;
use App\Models\RawFeedEmailFailed;
use App\Models\RawFeedFieldErrors;
use App\Models\Email;
use App\Repositories\FeedRepo;
use DB;

use Carbon\Carbon;

class RawFeedEmailRepo {
    const US_COUNTRY_ID = 1;

    protected $rawEmail;
    protected $failed;
    protected $failedFields;
    private $email;
    protected $feed;
    private $min;
    private $limit;
    protected $standardFields = [
        'feed_id' => 0 ,
        'party' => 0 ,
        'realtime' => 0 ,
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
        'dob' => 0 ,
        'file' => 0
    ];

    public function __construct ( RawFeedEmail $rawEmail , RawFeedEmailFailed $failed , Email $email , FeedRepo $feed , RawFeedFieldErrors $failedFields ) {
        $this->rawEmail = $rawEmail;
        $this->failed = $failed;
        $this->email = $email;
        $this->feed = $feed;
        $this->failedFields = $failedFields;
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
                    party ,
                    realtime ,
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
                    file ,
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
            if ( $fieldName == 'capture_date' ) {
                $cleanRecord[ $fieldName ] = $fieldValue; 
                continue;
            }

            $currentValue = preg_replace( '/[^\w\@\.\-\'\/\s:]/' , '' , $fieldValue );
            $currentValue = preg_replace( '/\s{2,}/' , '' , $currentValue );
            $currentValue = trim( $currentValue );

            $cleanRecord[ $fieldName ] = $currentValue; 
        }

        return $cleanRecord;
    }

    public function logRealtimeFailure ( $errors , $fullUrl , $referrerIp , $email = '' , $feedId = 0 ) {
        return $this->failed->create( [
            'realtime' => 1 ,
            'errors' => json_encode( $errors ) ,
            'url' => $fullUrl ,
            'ip' => $referrerIp ,
            'email' => $email ,
            'feed_id' => $feedId
        ] );
    }

    public function logBatchRealtimeFailure ( $errors , $csv , $file , $lineNumber , $email = '' , $feedId = 0 ) {
        return $this->failed->create( [
            'realtime' => 1 ,
            'errors' => json_encode( $errors ) ,
            'csv' => $csv ,
            'file' => $file ,
            'line_number' => $lineNumber ,
            'url' => '' ,
            'ip' => 'sftp-01.mtroute.com' ,
            'email' => $email ,
            'feed_id' => $feedId
        ] );
    }

    public function logBatchFailure ( $errors , $csv , $file , $lineNumber , $email = '' , $feedId = 0 ) {
        $csvToSave = $csv;

        if ( 'ISO-8859-15' === mb_detect_encoding( $csv , 'ASCII,UTF-8,ISO-8859-15' , true) ) {
            $csvToSave = iconv( 'ISO-8859-15' , 'UTF-8' , $csv );
        }

        try {
            return $this->failed->create( [
                'realtime' => 0 ,
                'errors' => json_encode( $errors ) ,
                'csv' => $csvToSave ,
                'file' => $file ,
                'line_number' => $lineNumber ,
                'url' => '' ,
                'ip' => 'sftp-01.mtroute.com' ,
                'email' => $email ,
                'feed_id' => $feedId
            ] );
        } catch ( \Exception $e ) {
            \Log::error( $e );
        }
    }

    public function logFieldFailure ( $field , $value , $errors , $rawFeedEmailFailedId = 0 ) {
        $this->failedFields->create( [
            'field' => $field ,
            'value' => $value ,
            'errors' => json_encode( $errors ) ,
            'raw_feed_email_failed_id' => $rawFeedEmailFailedId
        ] );
    }

    public function getFirstPartyRecordsFromFeed($startPoint, $feedId) {
        $output = [];

        $records = $this->rawEmail
                    ->where('feed_id', $feedId)
                    ->where('id', '>', $startPoint)
                    ->orderBy('id')
                    ->limit(1500)
                    ->get();

        foreach ($records as $record) {
            $search = $this->email
                ->selectRaw("email_domain_id, domain_group_id, emails.id as email_id")
                ->where('email_address', $record->email_address)
                ->leftJoin('email_domains as ed', 'emails.email_domain_id', '=', 'ed.id')
                ->first();

            if ($search) {
                $record->email_domain_id = $search->email_domain_id;
                $record->domain_group_id = $search->domain_group_id;
                $record->email_id = $search->email_id;
            }
            else {
                $record->email_domain_id = null;
                $record->domain_group_id = null;
                $record->email_id = null;
            }

            $output[] = $record;
        }

        return $output;
    }

    public function getThirdPartyRecordsWithChars($startPoint, $startChars) {
        $charsRegex = '^[' . $startChars . ']';

        $output = [];

        $emails = $this->rawEmail
                    ->whereRaw("raw_feed_emails.email_address RLIKE '$charsRegex'")
                    ->where('raw_feed_emails.id', '>', $startPoint)
                    ->whereRaw('party = 3')
                    ->orderBy('raw_feed_emails.id')
                    ->limit(1500)
                    ->get();

        foreach ($emails as $record) {
            $search = $this->email
                        ->selectRaw("email_domain_id, domain_group_id, emails.id as email_id")
                        ->where('email_address', $record->email_address)
                        ->leftJoin('email_domains as ed', 'emails.email_domain_id', '=', 'ed.id')
                        ->first();

            if ($search) {
                $record->email_domain_id = $search->email_domain_id;
                $record->domain_group_id = $search->domain_group_id;
                $record->email_id = $search->email_id;
            }
            else {
                $record->email_domain_id = null;
                $record->domain_group_id = null;
                $record->email_id = null;
            }

            $output[] = $record;
        }

        return $output;
    }
    public function getPullEmails($feedId,$startdate,$enddate,$min,$limit) {
        return  $this->rawEmail
                    ->selectRaw("id,email_address, source_url, ip, capture_date")
                    ->whereIn('feed_id', $feedId)
                    ->whereBetween('created_at', [$startdate,$enddate])
                    ->orderBy('id')
                    ->where('id', '>', $min)   
                    ->limit($limit);
    }
    protected function formatRecord ( $record ) {
        $pdo = \DB::connection()->getPdo();

        return "("
            . $pdo->quote( $record[ 'feed_id' ] ) . ","
            . $pdo->quote( $record[ 'party' ] ) . ","
            . $pdo->quote( $record[ 'realtime' ] ) . ","
            . $pdo->quote( $record[ 'email_address' ] ) . ","
            . $pdo->quote( $record[ 'source_url' ] ) . ","
            . ( isset( $record[ 'capture_date' ] ) ? $pdo->quote( $record[ 'capture_date' ] ) : 'NULL' ) . ","
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
            . ( isset( $record[ 'dob' ] ) ? $pdo->quote( $record[ 'dob' ] ) : 'NULL' ) . ","
            . ( isset( $record[ 'other_fields' ] ) ? $pdo->quote( $record[ 'other_fields' ] ) : '{}' ) . ","
            . ( isset( $record[ 'file' ] ) ? $pdo->quote( $record[ 'file' ] ) : '' ) . ","
            . "NOW() ,"
            . "NOW()"
        . ")";
    }

    protected function fixRecordStructure ( $record ) {
        $rawEmailRecord = array_intersect_key( $record , $this->standardFields );
        
        $customFields = array_diff_key( $record , $this->standardFields );

        $rawEmailRecord[ 'other_fields' ] = json_encode( $customFields );

        $this->formatDates( $rawEmailRecord );

        return $rawEmailRecord;
    }

    protected function formatDates ( &$rawEmailRecord ) {
        $isEuroDateFormat = ( $this->feed->getFeedCountry( $rawEmailRecord[ 'feed_id' ] ) !== self::US_COUNTRY_ID );

        try {
            if ( $isEuroDateFormat ) {
                $rawEmailRecord[ 'capture_date' ] = $this->convertEuropeanDate( $rawEmailRecord[ 'capture_date' ] );
            } else {
                try {
                    $rawEmailRecord[ 'capture_date' ] = Carbon::parse( $rawEmailRecord[ 'capture_date' ] )->toDateTimeString();
                } catch ( \Exception $e ) {
                    try {
                        $rawEmailRecord[ 'capture_date' ] = Carbon::createFromFormat( 'Y.m.d' , $rawEmailRecord[ 'capture_date' ] )->toDateTimeString();
                    } catch ( \Exception $e ) {
                        try {
                            $rawEmailRecord[ 'capture_date' ] = Carbon::createFromFormat( 'm/d/Y His A' , $rawEmailRecord[ 'capture_date' ] )->toDateTimeString();
                        } catch ( \Exception $e ) {
                            $rawEmailRecord[ 'capture_date' ] = Carbon::createFromFormat( 'n/j/Y G:i' , $rawEmailRecord[ 'capture_date' ] )->toDateTimeString();
                        }
                    }
                }
            }
        } catch ( \Exception $e ) {
            \Log::error( $e );

            unset( $rawEmailRecord[ 'capture_date' ] );
        }

        try {
            if( isset( $rawEmailRecord[ 'dob' ] ) && $rawEmailRecord[ 'dob' ] == '' ) {
                unset( $rawEmailRecord[ 'dob' ] );
            }

            if ( isset( $rawEmailRecord[ 'dob' ] ) ) {

                if ( $isEuroDateFormat ) {
                    $rawEmailRecord[ 'dob' ] = $this->convertEuropeanDate( $rawEmailRecord[ 'dob' ] );
                } else {
                    try {
                        $rawEmailRecord[ 'dob' ] = Carbon::parse( $rawEmailRecord[ 'dob' ] )->toDateString();
                    } catch ( \Exception $e ) {
                        $rawEmailRecord[ 'dob' ] = Carbon::createFromFormat( 'Y.m.d' , $rawEmailRecord[ 'dob' ] )->toDateString();
                    }
                }
            }
        } catch ( \Exception $e ) {
            \Log::error( $e );

            unset( $rawEmailRecord[ 'dob' ] );
        }

    }

    public function convertEuropeanDate ( $dateString ) {
        $date = null;

        try { #trying international standard format
            if ( preg_match( "/\-/" , $dateString ) === 0 ) {
                #not hyphen date format, skip down to forward slash format
                throw new \Exception();
            }

            $date = Carbon::parse( $dateString )->toDateString();
        } catch ( \Exception $e ) {   
            try { #trying forward slash format with year first
                $date = Carbon::createFromFormat( 'Y/d/m' , $dateString )->toDateString();
            } catch ( \Exception $e ) {   
                try { #trying forward slash format with day first
                    $date = Carbon::createFromFormat( 'd/m/Y' , $dateString )->toDateString();
                } catch ( \Exception $e ) {
                    try { #trying dates with periods 
                        $date = Carbon::createFromFormat( 'Y.m.d' , $dateString )->toDateString();
                    } catch ( \Exception $e ) {
                        try { #try dates with time
                            $date = Carbon::createFromFormat( 'd/m/y H:i:s' , $dateString )->toDateString();
                        } catch ( \Exception $e ) {
                            #all format parsing failed, leave null
                        }
                    }
                }
            }
        }

        return $date;
    }

    public function getMaxInvalidId() {
        return (int)$this->failed->max('id');
    }

    public function getInvalidBetweenIds($startId, $endId) {
        $startId = (int)$startId;
        $endId = (int)$endId;
        return $this->failed->whereBetween('id', [$startId, $endId])->orderBy('id');
    }

    public function getFirstPartyUnprocessed($minId, $date, $minInvalidId, $feed) {}

    public function getThirdPartyUnprocessed($minId, $date, $minInvalidId, $limit) {
        // Should test this to see if the lack of safeguards suffices

        return $this->rawEmail
                    ->leftJoin('emails as e', 'raw_feed_emails.email_address', '=', 'e.email_address')
                    ->leftJoin('email_domains as ed', 'e.email_domain_id', '=', 'ed.id')
                    ->leftJoin('email_feed_instances as efi', function($join) use ($date) {
                        $join->on('e.id', '=', 'efi.email_id');
                        $join->on('raw_feed_emails.feed_id', '=', 'efi.feed_id');
                        $join->where('efi.subscribe_date', '>=', $date); #despite the name, this keeps the value within the ON clause
                    })
                    ->leftJoin('invalid_email_instances as iei', function($join) use ($minInvalidId) {
                        $join->on('raw_feed_emails.email_address', '=', 'iei.email_address');
                        $join->on('raw_feed_emails.feed_id', '=', 'iei.feed_id');
                        $join->where('iei.id', '>', $minInvalidId);
                    })
                    ->leftJoin('suppression.suppression_global_orange as sgo', 'raw_feed_emails.email_address', '=', 'sgo.email_address')
                    ->whereRaw("party = 3 and raw_feed_emails.id > $minId")
                    ->whereNull("efi.email_id")
                    ->whereNull('sgo.email_address')
                    ->whereNull('iei.id')
                    ->where('raw_feed_emails.created_at', '<=', DB::raw("now() - interval 10 minute"))
                    ->selectRaw('raw_feed_emails.*, e.id as email_id, email_domain_id, domain_group_id')
                    ->orderBy('raw_feed_emails.id', 'asc')
                    ->take($limit)
                    ->get();

    }

    public function getMinId($datetime) {
        return $this->rawEmail->where('created_at', '>=', $datetime)->min('id');
    }

}
