<?php
namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;
class SuppressionReason extends Model
{
    protected $connection = 'mt1supp';
    protected $table = 'SuppressionReason';
}