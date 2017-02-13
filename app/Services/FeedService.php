<?php

namespace App\Services;

use App\Repositories\FeedRepo;
use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Models\RecordProcessingFileField;
use App\Repositories\CountryRepo;
use App\Services\ServiceTraits\PaginateList;
use App\Services\Interfaces\IFtpAdmin;
use App\Services\EmailFeedInstanceService;
use League\Csv\Writer;
use DB;
use Artisan;

class FeedService implements IFtpAdmin
{
    use PaginateList;

    private $feedRepo;
    private $verticals;
    private $feedTypes;
    private $countryRepo;
    private $instanceService;

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

    public function __construct(
        FeedRepo $feedRepo,
        CakeVertical $cakeVerticals ,
        CountryRepo $countryRepo ,
        FeedType $feedTypes ,
        EmailFeedInstanceService $instanceService
    ) {
        $this->feedRepo = $feedRepo;
        $this->verticals = $cakeVerticals;
        $this->feedTypes = $feedTypes;
        $this->countryRepo = $countryRepo;
        $this->instanceService = $instanceService;
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
        return $this->verticals->orderBy('name')->get();
    }

    public function getFeedTypes() {
        return $this->feedTypes->get();
    }

    public function getCountries() {
        return $this->countryRepo->get();
    }

    public function getModel( $searchData = null ) {
        return $this->feedRepo->getModel( $searchData );
    }

    public function getFeedCsv () {
        $model = $this->feedRepo->getModel();
        $results = $model->get();

        $writer = Writer::createFromFileObject( new \SplTempFileObject() );
        $writer->insertOne( [ "Feed ID" , "Client Name" , "Party" , "Feed Name" , "Feed Shortname" , "Password" , "Status" , "Vertical" , "Type" , "Country" , "Source" , "Created" , "Updated" ] );

        foreach ( $results as $row ) {
            $writer->insertOne( $row->toArray() );
        }

        return $writer->__toString();
    }

    public function updateOrCreate ( $data , $id = null ) {
        if ( empty( $data[ 'password' ] ) ) {
            $data[ 'password' ] = $this->generatePassword();
        }

        $this->feedRepo->updateOrCreate( $data , $id );
    }

    protected function generatePassword () {
        $password = str_random( 15 );

        while ( $this->feedRepo->passwordExists( $password ) ) {
            $password = str_random( 15 );
        }

        return $password;
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

    public function getFeedFields ( $feedId , $simpleArray = false ) {
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
                    if ( $simpleArray ) {
                        $fields[ $customIndex ] = $customName;
                    } else {
                        $fields[ $customIndex ] = [ "label" => $customName , "isCustom" => true ];
                    }
                }

                continue;
            }

            if ( $simpleArray ) {
                $fields[ $index ] = str_replace( '_index' , '' , $columnName);

                if ( $fields[ $index ] === 'email' ) {
                    $fields[ $index ] = 'email_address';
                }
            } else {
                $fields[ $index ] = $columnName;
            }
        }

        ksort( $fields );

        if ( $simpleArray ) {
            return $fields;
        }

        return json_encode( $fields );
    }

    public function findNewFtpUsers () {
        return $this->feedRepo->getNewUsersForToday();
    }

    public function resetPassword($username){
        Artisan::call('ftp:admin', [
            '-H' => config('ssh.servers.mt1_feed_file_server.host'),
            '-U' => config('ssh.servers.mt1_feed_file_server.username'),
            '-k' => config('ssh.servers.mt1_feed_file_server.public_key'),
            '-K' => config('ssh.servers.mt1_feed_file_server.private_key'),
            '-u' => $username,
            '-s' => "Feed",
            '-r' => true
        ]);
        return true;
    }

    public function getRecordCountForSource ( $search ) {
        $response = [ 'records' => $this->instanceService->getRecordCountForSource( $search ) ];

        if ( $search[ 'exportFile' ] === true && count( $response[ 'records' ] ) > 0 ) {
            $writer = Writer::createFromFileObject( new \SplTempFileObject() );
            $writer->insertOne( [ 'Client Name' , 'Feed Name' , 'Source URL' , 'Record Count' ] );

            foreach ( $response[ 'records' ] as $row ) {
                $writer->insertOne( [
                    $row[ 'clientName' ] ,
                    $row[ 'feedName' ] ,
                    $row[ 'sourceUrl' ] ,
                    $row[ 'count' ]
                ] );
            }

            $response[ 'csv' ] = $writer->__toString();
        }

        return $response;
    }

    public function getActiveFeedNames () {
        return $this->feedRepo->getActiveFeedNames();
    }

    public function getFileColumnMap ( $feedId ) {
        return $this->getFeedFields( $feedId , true );
    }

    public function getCountryFeedMap(){
        $map = [];
        $feeds = $this->feedRepo->getFeeds();

        foreach ($feeds as $feed) {

            $map[$feed->country_id][] = $feed->id;
        }

        return $map;
    }

    public function getPartyFeedMap(){

        $map = [];
        $feeds = $this->feedRepo->getFeeds();

        foreach ($feeds as $feed) {

            $map[$feed->party][] = $feed->id;
        }

        return $map;
    }

    public function getPaginatedJson($page, $count, $params = null)
    {
        $searchData = null;
        if ($this->hasCache($page, $count, $params)) {
            return $this->getCachedJson($page, $count, $params);
        } else {
            try {

                $searchData = isset($params['data']) ? $params['data'] : null;
                $eloquentObj = $this->getModel($searchData);

                if ( isset( $params['sort'] ) ){
                    $sort = json_decode( $params['sort'] , true );

                    $order = 'asc';

                    if ( isset( $sort[ 'desc' ] ) && $sort[ 'desc' ] === true ) {
                        $order = 'desc';
                    }

                    $eloquentObj = $eloquentObj->orderByRaw(\DB::raw( $sort['field'] . ' ' .  $order ) );
                }

                $paginationJSON = $eloquentObj->paginate($count)->toJSON();

                $this->cachePagination(
                    $paginationJSON,
                    $page,
                    $count, $params
                );

                return $paginationJSON;
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function updatePassword ( $shortName , $password ) {
        $this->feedRepo->updatePassword( $shortName , $password );
    }

    public function saveFtpUser ( $credentials ) {}
}
