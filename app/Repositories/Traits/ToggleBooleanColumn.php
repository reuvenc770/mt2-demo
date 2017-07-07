<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories\Traits;

trait ToggleBooleanColumn {
    public function toggleBooleanColumn ( $model , $id , $columnName , $currentStatus ) {
        $record = $model->find( $id );
            
        $record->$columnName = !(bool)$currentStatus;

        return $record->save();
    }
}
