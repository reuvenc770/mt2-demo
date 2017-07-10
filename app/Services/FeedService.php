<?php

namespace App\Services;

use App\Models\FeedVertical;
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
//Todo not happy with lack of Repo's this has way to much repo logic in it.
class FeedService implements IFtpAdmin
{
    use PaginateList;

    const US_COUNTRY_ID = 1;

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
        FeedVertical $feedVertical ,
        CountryRepo $countryRepo ,
        FeedType $feedTypes ,
        EmailFeedInstanceService $instanceService
    ) {
        $this->feedRepo = $feedRepo;
        $this->verticals = $feedVertical;
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

    public function getFeedIdByShortName ( $name ) {
        return $this->feedRepo->getFeedIdByShortName( $name );
    }

    public function getFeedIdFromPassword ( $password ) {
        return $this->feedRepo->getFeedIdFromPassword( $password );
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

    public function getFeedCountry ( $feedId ) {
        return $this->feedRepo->getFeedCountry( $feedId );
    }

    public function getModel( $searchData = null ) {
        return $this->feedRepo->getModel( $searchData );
    }

    public function getFeedCsv () {
        $model = $this->feedRepo->getModel('');
        $results = $model->get();

        $writer = Writer::createFromFileObject( new \SplTempFileObject() );
        $writer->insertOne( [ "Feed ID" , "Client Name" , "Party" , "Feed Name" , "Feed Shortname" , "Password" , "Host", "Status" , "Vertical" , "Type" , "Country" , "Source" , "Created" , "Updated" ] );

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
            if ( $simpleArray ) {
                return [];
            } else {
                return json_encode( [] );
            }
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

    public function getActiveFeedShortNames () {
        return $this->feedRepo->getActiveFeedShortNames();
    }

    public function getFileColumnMap ( $feedId ) {
        return $this->getFeedFields( $feedId , true );
    }

    public function getCountryFeedMap(){
        return $this->feedRepo->getCountryFeedMap();
    }

    public function getPartyFeedMap(){
        return $this->feedRepo->getPartyFeedMap();
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

    public function generateValidationRules ( $data ) {
        $isRealtime = false;

        $rules = [
            'ip' => 'required|ip' ,
            'source_url' => 'required'
        ];

        $emailRule = 'required|email';
        $feedPasswordRule = 'required|exists:feeds,password';
        $euroDateRule = 'required|euroDate';
        $usDateRule = 'required|date';

        if ( isset( $data[ 'pw' ] ) && $data[ 'pw' ] != '' ) {
            $isRealtime = true;
            $rules[ 'email' ] = $emailRule;
            $rules[ 'pw' ] = $feedPasswordRule;
        } else {
            $rules[ 'email_address' ] = $emailRule; 
        }

        if ( $isRealtime ) {
            $feedId = $this->feedRepo->getFeedIdFromPassword( $data[ 'pw' ] );
        } else {
            $feedId = $data[ 'feed_id' ];
        }

        #We only have US and UK feeds right now
        $isBritishRecord = ( $this->feedRepo->getFeedCountry( $feedId ) !== self::US_COUNTRY_ID );
        $realtimeDobExists = ( isset( $data[ 'birth_date' ] ) && $data[ 'birth_date' ] != '' ); 
        $batchDobExists = ( isset( $data[ 'dob' ] ) && $data[ 'dob' ] != '' );

        if ( $isBritishRecord ) {
            $rules[ 'capture_date' ] = $euroDateRule . '|euroDateNotFuture';
        } else {
            $rules[ 'capture_date' ] = $usDateRule . '|before:tomorrow';
        }

        if ( $isBritishRecord && $realtimeDobExists ) {
            $rules[ 'birth_date' ] = $euroDateRule;
        } elseif ( !$isBritishRecord && $realtimeDobExists ) {
            $rules[ 'birth_date' ] = $usDateRule;
        }

        if ( $isBritishRecord && $batchDobExists ) {
            $rules[ 'dob' ] = $euroDateRule;
        } elseif ( !$isBritishRecord && $batchDobExists ) {
            $rules[ 'dob' ] = $usDateRule;
        }

        return $rules;
    }

    public function getFeedNameFromId ( $id ) {
        return $this->feedRepo->getFeedNameFromId( $id );
    }
}
