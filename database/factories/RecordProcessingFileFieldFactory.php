<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\RecordProcessingFileField::class , function ( Faker\Generator $faker ) {
    $requiredColumns = [ 'email_index' , 'source_url_index' , 'capture_date_index' , 'ip_index' ];
    $personColumns = [ 'first_name_index' , 'last_name_index' , 'gender_index' , 'dob_index' ];
    $addressColumns = [ 'address_index' , 'address2_index' , 'city_index' , 'state_index' , 'zip_index' , 'country_index' ];

    $customColumns = [
        'insurance' => [ 'Has Insurance' , 'Has Medicaid' ] ,
        'dating' => [ 'Relationship Status' , 'Gender Preference' ] ,
        'education' => [ 'Level of Education' , 'Need Financing' ]
    ];

    $finalFieldList = $requiredColumns;

    $addAllPersonFields = ( rand( 0 , 3 ) == 1 );
    if ( $addAllPersonFields ) {
        $finalFieldList = array_merge( $finalFieldList , $personColumns );
    } else {
        array_push( $finalFieldList , $personColumns[ 0 ] , $personColumns[ 1 ] );
    }

    $addAddressFields = ( rand( 0 , 1 ) == 1 );
    if ( $addAddressFields ) {
        $finalFieldList = array_merge( $finalFieldList , $addressColumns );
    }

    shuffle( $finalFieldList );

    $finalFieldList = array_flip( $finalFieldList );

    $addCustomFields = ( rand( 0 , 1 ) == 1 );
    if ( $addCustomFields ) {
        $types = array_keys( $customColumns );
        shuffle( $types );

        $chosenCustomColumns = $customColumns[ array_pop( $types ) ];
        $otherColumns = [ 'other_field_index' => json_encode(
            array_combine(
                $chosenCustomColumns , 
                array_keys( array_fill( count( $finalFieldList ) , count( $chosenCustomColumns ) , 1 ) )
            )
        ) ];

        $finalFieldList = array_merge( $finalFieldList , $otherColumns );
    }

    return $finalFieldList;
} );

