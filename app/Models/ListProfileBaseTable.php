<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileBaseTable extends Model {

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'email_id';
    protected $connection = 'list_profile_export_tables';
    
    public function __construct($tableName) {
        parent::__construct([]);
        $this->table = $tableName;
    }
}