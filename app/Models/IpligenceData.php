<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IpligenceData
 *
 * @property int $ip_from
 * @property int $ip_to
 * @property string $country_code
 * @property string $country_name
 * @property string $continent_code
 * @property string $continent_name
 * @property string $time_zone
 * @property string $region_code
 * @property string $region_name
 * @property string $owner
 * @property string $city_name
 * @property string $county_name
 * @property string $post_code
 * @property string $metro_code
 * @property string $area_code
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereAreaCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereCityName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereContinentCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereContinentName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereCountryCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereCountryName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereCountyName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereIpFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereIpTo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereMetroCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereOwner($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData wherePostCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereRegionCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereRegionName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\IpligenceData whereTimeZone($value)
 * @mixin \Eloquent
 */
class IpligenceData extends Model {
    protected $table = 'ipligence_data';
}
