<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailList;
use App\Models\MT1Models\LiveEmailList;
use App\Repositories\RepoInterfaces\Mt1Import;
use DB;

class EmailListRepo implements Mt1Import {
    protected $model;
    private $liveModel;

    public function __construct ( EmailList $model, LiveEmailList $liveModel ) {
        $this->model = $model;
        $this->liveModel = $liveModel;
    }

    public function insertToMt1($data) {
        $this->liveModel->updateOrCreate(['email_user_id' => $data['email_user_id']], $data);
    }

    public function getThirdPartyForAddress($emailAddress) {
        return $this->model
                    ->join('user as u', 'email_list.client_id', '=', 'u.user_id')
                    ->where('email_list.email_addr', $emailAddress)
                    ->where('OrangeClient', 'Y')
                    ->select('email_list.email_user_id as email_id', 
                        'u.user_id as feed_id', 
                        'email_list.subscribe_date',
                        DB::raw('CONCAT(email_list.subscribe_date, " ", subscribe_time) as subscribe_datetime'),
                        'email_list.first_name',
                        'email_list.last_name',
                        'email_list.address',
                        'email_list.address2',
                        'email_list.city',
                        'email_list.state',
                        'email_list.zip',
                        'email_list.country',
                        DB::raw('IF(email_list.dob = "0000-00-00", null, dob) as dob'),
                        DB::raw('IF(email_list.gender = "", "UNK", gender) as gender'),
                        'email_list.phone',
                        'email_list.capture_date',
                        'email_list.member_source as ip',
                        'email_list.source_url',
                        'email_list.emailUserActionDate as last_action_date',
                        DB::raw('CASE emailUserActionTypeID 
                            WHEN 1 THEN "Open" 
                            WHEN 2 THEN "Click" 
                            WHEN 3 THEN "Conversion" 
                            ELSE "None" 
                        END as last_action_type'))
                    ->first();
    }
}
