<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\SuppressionGlobalOrange;
use App\Repositories\RepoInterfaces\IAwsRepo;
use DB;

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
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
            . $pdo->quote($row->email_address) . ','
            . $pdo->quote($row->suppress_datetime) . ','
            . $pdo->quote($row->reason_id) . ','
            . $pdo->quote($row->type_id) . ','
            . $pdo->quote($row->created_at) . ','
            . $pdo->quote($row->updated_at);
    }

    public function extractAllForS3() {
        return $this->model->whereRaw("created_at >= CURDATE() - INTERVAL 10 DAY");
    }

    public function specialExtract($data) {}

    public function getConnection() {
        return $this->model->getConnectionName();
    }

    public function getCount($maxLookback) {
        return $this->model
                    ->whereRaw("created_at <= CURDATE() - INTERVAL $maxLookback DAY")
                    ->count();
    }

    public function getAllQuery($lookback) {
        return $this->model->whereRaw("created_at <= CURDATE() - INTERVAL $lookback DAY")->toSql();
    }

    public function returnSuppressedEmails(array $emails) {
        return $this->model->whereIn('email_address', $emails)->select('email_address')->get();
    }

    public function isSuppressed($emailAddress) {
        return $this->model->where('email_address', $emailAddress)->count() > 0;
    }

}
