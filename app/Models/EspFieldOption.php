<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspFieldOption
 *
 * @property int $esp_id
 * @property string $email_id_field
 * @property string $open_email_id_field
 * @property string $email_address_field
 * @property string $open_email_address_field
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Esp $esp
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereEmailAddressField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereEmailIdField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereEspId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereOpenEmailAddressField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereOpenEmailIdField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspFieldOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspFieldOption extends Model {
    
    protected $primaryKey = 'esp_id';
    protected $guarded = [];

    public function esp() {
        return $this->belongsTo('App\Models\Esp');
    }
}
