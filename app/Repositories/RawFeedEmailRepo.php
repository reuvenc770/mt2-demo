<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\RawFeedEmail;
use App\Models\RawFeedEmailFailed;
use App\Models\Email;
use App\Services\FeedService;

use Carbon\Carbon;

class RawFeedEmailRepo {
    const US_COUNTRY_ID = 1;

    protected $rawEmail;
    protected $failed;
    private $email;

    protected $feedService;

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

    public function __construct ( RawFeedEmail $rawEmail , RawFeedEmailFailed $failed , Email $email , FeedService $feedService ) {
        $this->rawEmail = $rawEmail;
        $this->failed = $failed;
        $this->email = $email;
        $this->feedService = $feedService;
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
        $output = [];

        $records = $this->rawEmail
                    ->where('feed_id', $feedId)
                    ->where('id', '>', $startPoint)
                    ->orderBy('id')
                    ->limit(1000)
                    ->get();

        foreach ($records as $email) {
            $search = $this->email
                ->selectRaw("email_domain_id, domain_group_id, emails.id as email_id")
                ->where('email_address', $record->email_address)
                ->join('email_domains as ed', 'emails.email_domain_id', '=', 'ed.id')
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
                    ->orderBy('raw_feed_emails.id')
                    ->limit(1000)
                    ->get();

        foreach ($emails as $record) {
            $search = $this->email
                        ->selectRaw("email_domain_id, domain_group_id, emails.id as email_id")
                        ->where('email_address', $record->email_address)
                        ->join('email_domains as ed', 'emails.email_domain_id', '=', 'ed.id')
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

            $suppressed = \DB::connection('suppression')
                            ->table('suppression_global_orange')
                            ->where('email_address', $record->email_address)
                            ->first();

            if ($suppressed) {
                $record->suppressed = 1;
            }
            else {
                $record->suppressed = 0;
            }

            $output[] = $record;
        }

        return $output;
    }

    protected function formatRecord ( $record ) {
        $pdo = \DB::connection()->getPdo();

        return "("
            . $pdo->quote( $record[ 'feed_id' ] ) . ","
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
            . "NOW() ,"
            . "NOW()"
        . ")";
    }

    protected function fixRecordStructure ( $record ) {
        $rawEmailRecord = array_intersect_key( $record , $this->standardFields );
        
        $customFields = array_diff_key( $record , $this->standardFields );

        $rawEmailRecord[ 'other_fields' ] = json_encode( $customFields );

        try {
            $rawEmailRecord[ 'capture_date' ] = Carbon::parse( $rawEmailRecord[ 'capture_date' ] )->toDateTimeString();
        } catch ( \Exception $e ) {
            \Log::error( $e );

            unset( $rawEmailRecord[ 'capture_date' ] );
        }

        try {
            if( isset( $rawEmailRecord[ 'dob' ] ) && $rawEmailRecord[ 'dob' ] == '' ) {
                unset( $rawEmailRecord[ 'dob' ] );
            }

            if ( isset( $rawEmailRecord[ 'dob' ] ) ) {
                $isEuroDateFormat = ( $this->feedService->getFeedCountry( $rawEmailRecord[ 'feed_id' ] ) !== self::US_COUNTRY_ID );

                if ( $isEuroDateFormat ) {
                    $rawEmailRecord[ 'dob' ] = Carbon::createFromFormat('d/m/Y', $rawEmailRecord[ 'dob' ] )->toDateString();
                } else {
                    $rawEmailRecord[ 'dob' ] = Carbon::parse( $rawEmailRecord[ 'dob' ] )->toDateString();
                }
            }
        } catch ( \Exception $e ) {
            \Log::error( $e );

            unset( $rawEmailRecord[ 'dob' ] );
        }

        return $rawEmailRecord;
    }
}
