<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileCombinePivot
 *
 * @property int $list_profile_id
 * @property int $list_profile_combine_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombinePivot whereListProfileCombineId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombinePivot whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileCombinePivot extends Model {
    protected $connection = "list_profile";
    protected $table = "list_profile_list_profile_combine";    
    protected $primaryKey = 'list_profile_combine_id';

    public function getTable() {
        return $this->connection . '.' . $this->table; 
    }
}
