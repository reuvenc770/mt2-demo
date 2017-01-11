<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/6/17
 * Time: 12:41 PM
 */

namespace App\Repositories;


use App\Models\AweberEmailActionsStorage;
use App\Models\ActionType;
use DB;
class AWeberEmailActionsRepo
{
    private $model;

    public function __construct(AweberEmailActionsStorage $storage)
    {
        $this->model = $storage;
    }

    public function massRecordDeliverables ( $records = [] ) {
        foreach ( $records as $currentIndex => $currentRecord ) {
                $emailId = $this->getEmailId($currentRecord['email']);
                $validRecord = "( "
                    . join( " , " , [
                        $emailId ,
                        $currentRecord[ 'espId' ] ,
                        $currentRecord['deployId'],
                        $currentRecord[ 'espInternalId' ] ,
                        $this->getActionId( $currentRecord[ 'recordType' ] ) ,
                        ( empty( $currentRecord[ 'date' ] ) ? "''" : "'" . $currentRecord[ 'date' ] . "'" ) ,
                        'NOW()' ,
                        'NOW()'
                    ] )
                    . " )";
                $validRecords []= $validRecord;
            }
        
        if ( !empty( $validRecords ) ) {
            $chunkedRecords = array_chunk( $validRecords , 10000 );
            foreach ( $chunkedRecords as $chunkIndex => $chunk ) {
                DB::connection( 'reporting_data' )->statement("
                    INSERT INTO a_weber_email_actions_storages
                        ( email_id , esp_account_id , deploy_id, 
                        esp_internal_id , action_id , datetime , created_at , 
                        updated_at )    
                    VALUES
                        " . join( ' , ' , $chunk ) . "
                    ON DUPLICATE KEY UPDATE
                        email_id = email_id ,
                        esp_account_id = esp_account_id ,
                        deploy_id = deploy_id,
                        esp_internal_id = esp_internal_id ,
                        action_id = action_id ,
                        datetime = datetime ,
                        created_at = created_at ,
                        updated_at = NOW()"
                );
            }
        }
        $validRecords = null;
    }

    public function getEmailId($fullUrl){
        return substr($fullUrl, strrpos($fullUrl, '/') + 1);
    }

    protected function getActionId ( $actionName ) {
        return ActionType::where( 'name' , $actionName )->first()->id;
    }
}