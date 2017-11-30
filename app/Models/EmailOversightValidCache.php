<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOversightValidCache extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'email';
}
