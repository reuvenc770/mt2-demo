<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspCampaignMapping
 *
 * @property int $id
 * @property string $mappings
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $esp_id
 * @property-read \App\Models\Esp $esp
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspCampaignMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspCampaignMapping whereEspId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspCampaignMapping whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspCampaignMapping whereMappings($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspCampaignMapping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspCampaignMapping extends Model
{
    protected $guarded = [];
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

}
