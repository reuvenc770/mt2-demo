<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\SuppressionGlobalOrange;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\EtlPickupRepo;

class SuppressionGlobalOrangeRepo implements IAwsRepo {
    protected $model;

    public function __construct ( SuppressionGlobalOrange $model ) {
        $this->model = $model;
    }

    public function updateOrCreate ( $data ) {
        $this->model->updateOrCreate( [ 'email_address' => $data[ 'email_address' ] ] , $data );
    }

    public function extractForS3Upload(EtlPickupRepo $pickupRepo) {
        $startPoint = $pickupRepo->getLastInsertedForName('SuppressionGlobalOrange-s3');
        return $this->emailModel->whereRaw("id > $startPoint");
    }

    public function mapForS3Upload($row) {
        return [
            'id' => $row->id,
            'email_address' => $row->email_address,
            'suppress_datetime' => $row->suppress_datetime,
            'reason_id' => $row->reason_id,
            'type_id' => $row->type_id,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];
    }
}
