<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\YmlpCampaign
 *
 * @property int $id
 * @property int $esp_account_id
 * @property string $date
 * @property string $sub_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereSubId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpCampaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class YmlpCampaign extends Model {
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

}