<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RelevantToolsReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property string $campaign_name
 * @property int $total_sent
 * @property int $total_open
 * @property string $datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereCampaignName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereTotalOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereTotalSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RelevantToolsReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelevantToolsReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "datetime";
    }

    public function getSubjectFieldName(){
        return null;
    }
}
