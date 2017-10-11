<?php

namespace App\Jobs;

use Carbon\Carbon;
use Mail;

class WarnIPv6Update extends MonitoredJob {

    private $lookback;
    private $jobName = 'WarnIpv6Update';

    public function __construct($tracking, $runtimeThreshold = null) {
        $this->lookback = $lookback;

        parent::__construct($this->jobName, $runtimeThreshold, $tracking);
    }

    protected function handleJob() {
        $repo = App::make(\App\Repositories\Ipv6CountryMappingRepo::class);
        $updateDay = $repo->getLastUpdate();

        // We want to update this once a month or so
        if (Carbon::today()->gt(Carbon::parse($updateDate)->addDays(30))) {
            Mail::raw("IPv6 data last updated at $updateDay. Please download ipv6 to country mapping file, please in storage/app directory, and run UploadIPv6DB. See expected file format in UploadIPv6DBJob. MaxMind GeoIPv6 Country should match this format.", function ($message) {
                $message->to(config('contacts.tech'));
                $message->subject("Alert! IPv6 definitions may be out of date.");
            });
        }
    }
}
