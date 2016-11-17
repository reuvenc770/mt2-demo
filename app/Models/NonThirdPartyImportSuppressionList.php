<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonThirdPartyImportSuppressionList extends Model {

    protected $guarded = [];
    protected $connection = 'suppression';
}
