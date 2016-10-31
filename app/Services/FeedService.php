<?php

namespace App\Services;

use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Models\RecordProcessingFileField;
use App\Repositories\CountryRepo;
use App\Repositories\FeedRepo;
use App\Services\ServiceTraits\PaginateList;

class FeedService
{
    use PaginateList;

    private $verticals;
    private $feedTypes;
    private $countryRepo;
    private $feedRepo;

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

    public function __construct( CakeVertical $cakeVerticals , CountryRepo $countryRepo , FeedRepo $feedRepo , FeedType $feedTypes ) {
        $this->verticals = $cakeVerticals;
        $this->feedTypes = $feedTypes;
        $this->countryRepo = $countryRepo;
        $this->feedRepo = $feedRepo;
    }

    public function getFeeds () {
        return $this->feedRepo->getFeeds();
    }

    public function getFeed($id) {
        return $this->feedRepo->fetch($id);
    }

    public function getClientTypes() {
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
}
