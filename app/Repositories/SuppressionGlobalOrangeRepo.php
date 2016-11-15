<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\SuppressionGlobalOrange;

class SuppressionGlobalOrangeRepo {
    protected $model;

    public function __construct ( SuppressionGlobalOrange $model ) {
        $this->model = $model;
    }

    public function updateOrCreate ( $data ) {
        $this->model->updateOrCreate( [ 'id' => $data[ 'id' ] ] , $data );
    }
}
