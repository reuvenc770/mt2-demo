<?php

namespace App\Repositories;

use App\Facades\EspApiAccount;
use App\Models\YmlpCampaign;

class YmlpCampaignRepo {
    /**
     * @var IReport
     */
    protected $model;

    public function __construct(YmlpCampaign $model ){
        $this->model = $model;
    }

    public function getMtCampaignNameForAccountAndDate($espAccountId, $date) {
        
        $whereClause = array(
            'esp_account_id' => $espAccountId, 
            'date' => $date
        );
        
        $record = $this->model->select('sub_id')->where($whereClause)->first();
            
        if ( !is_null( $record ) ) return $record['sub_id'];
        else return '';
    }

    public function updateCampaign ($id , $accountData ) {
       return  $this->model->where( 'id' , $id )->update( [
            'sub_id' => $accountData[ 'sub_id' ] ,
            'date' => $accountData[ 'date' ] ,
            'esp_account_id' => $accountData[ 'esp_account_id' ]
        ] );
    }

    public function insertCampaign($data){
        $this->model->sub_id = $data[ 'sub_id' ];
        $this->model->esp_account_id = $data[ 'esp_account_id' ];
        $this->model->date = $data[ 'date' ];

       return $this->model->save();
    }

    public function getCampaigns(){
       return $this->model->all();
    }

    public function getById($id){
        return $this->model->find($id);
    }

    public function getModel () {
        return $this->model; 
    }
}
