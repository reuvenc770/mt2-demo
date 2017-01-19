<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\SuppressionGlobalOrange;
use App\Repositories\RepoInterfaces\IAwsRepo;

class SuppressionGlobalOrangeRepo implements IAwsRepo {
    protected $model;

    public function __construct ( SuppressionGlobalOrange $model ) {
        $this->model = $model;
    }

    public function updateOrCreate ( $data ) {
        $this->model->updateOrCreate( [ 'email_address' => $data[ 'email_address' ] ] , $data );
    }

    public function extractForS3Upload($startPoint) {
        return $this->model->whereRaw("id > $startPoint");
    }

    public function mapForS3Upload($row) {
        return [
            $row->id,
            $row->email_address,
            $row->suppress_datetime,
            $row->reason_id,
            $row->type_id,
            $row->created_at,
            $row->updated_at
        ];
    }

    public function extractAllForS3() {
        return $this->model;
    }

}
