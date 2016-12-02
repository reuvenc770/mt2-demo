<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\MD5AdvertiserSuppressList;

class MD5AdvertiserSuppressListRepo {
    protected $model;

    public function __construct ( MD5AdvertiserSuppressList $model ) {
        $this->model = $model;
    }

    public function isSuppressed ( $record , $advertiserId) {
        return $this->model->where(
            [ 'advertiser_id' , $advertiserId ] ,
            [ 'md5sum' , $record ]
        )->count() > 0;
    }
}
