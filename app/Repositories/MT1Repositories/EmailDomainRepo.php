<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailDomain;
use App\Repositories\RepoInterfaces\Mt1Import;

class EmailDomainRepo implements Mt1Import {
    const LAST_ID_FIELD = 'LastSuppressedEmailDomainId';

    protected $model;

    public function __construct ( EmailDomain $model ) {
        $this->model = $model;
    }

    public function pullForSync ( $lookback ) {
        $etlPickup = \App::make( App\Models\EtlPickup::class );

        $id = $etlPickup->where( 'name' , self::LAST_ID_FIELD )->first()->stop_point;

        return $this->model->where( 'domain_id' , '>=' , $id );
    }
}
