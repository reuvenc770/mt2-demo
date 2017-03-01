<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedDateEmailBreakdown
 *
 * @property int $id
 * @property int $feed_id
 * @property string $date
 * @property int $domain_group_id
 * @property int $total_emails
 * @property int $valid_emails
 * @property int $suppressed_emails
 * @property int $unique_emails
 * @property int $feed_duplicates
 * @property int $cross_feed_duplicates
 * @property int $phone_counts
 * @property int $full_postal_counts
 * @property int $bad_source_urls
 * @property int $bad_ip_addresses
 * @property int $other_invalid
 * @property int $suppressed_domains
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereBadIpAddresses($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereBadSourceUrls($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereCrossFeedDuplicates($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereDomainGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereFeedDuplicates($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereFullPostalCounts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereOtherInvalid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown wherePhoneCounts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereSuppressedDomains($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereSuppressedEmails($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereTotalEmails($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereUniqueEmails($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedDateEmailBreakdown whereValidEmails($value)
 * @mixin \Eloquent
 */
class FeedDateEmailBreakdown extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
