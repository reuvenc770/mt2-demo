<?php

namespace App\Services;

use App\Repositories\FeedRepo;
use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Models\RecordProcessingFileField;
use App\Repositories\CountryRepo;
use App\Services\ServiceTraits\PaginateList;
use App\Services\Interfaces\IFtpAdmin;
use DB;

class FeedService implements IFtpAdmin
{
    use PaginateList;
    
    private $feedRepo;
    private $verticals;
    private $feedTypes;
    private $countryRepo;

    private $optionalFields = [
        'first_name_index' ,
        'last_name_index' ,
        'address_index' ,
        'address2_index' ,
        'city_index' ,
        'state_index' ,
        'zip_index' ,
        'country_index' ,
        'gender_index' ,
        'phone_index' ,
        'dob_index' ,
        'other_field_index'
    ];

    public function __construct(FeedRepo $feedRepo, CakeVertical $cakeVerticals , CountryRepo $countryRepo , FeedType $feedTypes) {
        $this->feedRepo = $feedRepo;
        $this->verticals = $cakeVerticals;
        $this->feedTypes = $feedTypes;
        $this->countryRepo = $countryRepo;
    }

    public function getAllFeedsArray() {
        return $this->feedRepo->getAllFeedsArray();
    }

    
    public function getFeeds () {
        return $this->feedRepo->getFeeds();
    }


    public function getClientFeedMap () {
        $map = [];
        $clients = $this->feedRepo->getAllClients();

        foreach ($clients as $client) {
            $feeds = [];

            foreach ($client->feeds as $feed) {
                $feeds[] = $feed->id;
            }

            $map[$client->id] = $feeds;
        }

        return $map;
    }

    public function getFeed($id) {
        return $this->feedRepo->fetch($id);
    }

    public function getFeedIdByName ( $name ) {
        return $this->feedRepo->getFeedIdByName( $name );
    }

    public function getVerticals() {
        return $this->verticals->get();
    }

    public function getFeedTypes() {
        return $this->feedTypes->get();
    }

    public function getCountries() {
        return $this->countryRepo->get();
    }

    public function getModel() {
        return $this->feedRepo->getModel();
    }

    public function updateOrCreate ( $data , $id = null ) {
        $this->feedRepo->updateOrCreate( $data , $id );
    }

    public function getType () {
        return 'Feed';
    }

    public function saveFieldOrder ( $feedId , $fieldConfig ) {
        if ( isset( $fieldConfig[ 'other_field_index' ] ) ) {
            $fieldConfig[ 'other_field_index' ] = json_encode( $fieldConfig[ 'other_field_index' ] );
        }

        foreach ( $this->optionalFields as $field ) {
            if ( !isset( $fieldConfig[ $field ] ) ) {
                $fieldConfig[ $field ] = null;
            }
        }

        RecordProcessingFileField::updateOrCreate( [ 'feed_id' => $feedId ] , $fieldConfig );
    }

    public function getFeedFields ( $feedId ) {
        $row = RecordProcessingFileField::find( $feedId );

        if ( is_null( $row ) ) {
            return json_encode( [] );
        }

        $row = $row->toArray();

        $fields = [];
        foreach ( $row as $columnName => $index ) {
            if ( is_null( $index ) || in_array( $columnName , [ 'feed_id' , 'created_at' , 'updated_at' ] ) ) {
                continue;
            }

            if ( $columnName === 'other_field_index' ) {
                $customFields = json_decode( $index );

                foreach ( $customFields as $customName => $customIndex ) {
                    $fields[ $customIndex ] = [ "label" => $customName , "isCustom" => true ];
                }

                continue;
            }

            $fields[ $index ] = $columnName;
        }

        ksort( $fields );

        return json_encode( $fields );
    }

    public function saveFtpUser ( $credentials ) {
        \Log::info( 'Saving user credentials to db. Creds: ' . json_encode( $credentials ) );

        DB::connection( 'mt1_data' )->table( 'user' )
            ->where( 'username' , $credentials[ 'username' ] )
            ->update( [ 'ftp_pw' => $credentials[ 'password' ],
                'ftp_user' => $credentials[ 'username' ],
                'ftp_url' => $credentials['ftp_url'],
                'newClient' => 0 ] );
    }

    public function findNewFtpUsers () {
        return DB::connection( 'mt1_data' )->table( 'user' )
            ->select( 'username' )
            ->where( [ 'newClient' => 1 , 'ftp_user' => '' ] )
            ->get();
    }

    public function resetPassword($username){
        Artisan::queue('ftp:admin', [
            '-H' => "52.205.67.250",
            '-U' => 'root',
            '-k' => '~/.ssh/mt2ftp.pub',
            '-K' => '~/.ssh/mt2ftp',
            '-u' => $username,
            '-s' => "Client",
            '-r' => true
        ]);
        return true;
    }

    public function getActiveFeedNames () {
        return $this->feedRepo->getActiveFeedNames();
    }

    public function getFileColumnOrder ( $feedId ) {
        return $this->feedRepo->getFileColumnOrder( $feedId );
    }
}
