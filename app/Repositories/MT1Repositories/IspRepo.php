<?php

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class IspRepo {
    public function __construct () {}

    public function getAll () {
        try{
            return DB::connection('mt1mail')->table('email_class')
                ->select( 'class_id as id' , 'class_name as name' )
                ->where( 'status' , 'Active' )
                ->get();

        } catch (\Exception $e){
            Log::error("IspRepo error:: ".$e->getMessage());
        }
    }
}
