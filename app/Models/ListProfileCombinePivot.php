<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileCombinePivot extends Model {
    protected $connection = "list_profile";
    protected $table = "list_profile_list_profile_combine";    
    protected $primaryKey = 'list_profile_combine_id';

    public function getTable() {
        return $this->connection . '.' . $this->table; 
    }
}
