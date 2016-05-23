<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\DataExport;
use Log;

class DataCleanseRepo {
    protected $model;

    public function __construct ( DataExport $model ) {
        $this->model = $model;
    }

    public function getType () {
        return 'datacleanse';
    }

    public function getModel () {
        return $this->model::select(
                'exportID as id' ,
                'fileName as name' ,
                'lastUpdated' ,
                'recordCount as count'
            )->where( [
                [ 'exportType' , 'Cleanse' ] ,
                [ 'status' , 'Active' ]
            ] )
            ->orderBy( 'fileName' , 'ASC' );
    }
}
