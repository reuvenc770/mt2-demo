<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileBaseTable extends Model {

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'email_id';
    protected $connection = 'list_profile_export_tables';
    protected $table;
    
    public function __construct($tableName) {
        parent::__construct([]);
        $this->table = $tableName;
    }

    public function getTable() {
        return $this->table;
    }

    public function isGloballySuppressed() {
        return (int)$this->globally_suppressed === 1;
    }

    public function isFeedSuppressed() {
        return (int)$this->feed_suppressed === 1;
    }
}