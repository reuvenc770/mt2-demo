<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateCakeEncryption extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'AffiliateCakeEncryption';
    protected $primaryKey = 'affiliateID';
    
    // The primary key in this table is a compound key, which Doctrine does not support, hence the incorrect declaration.
    // Hopefully this doesn't break anything.
}