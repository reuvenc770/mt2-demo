<?php

namespace App\Services;
use App\Repositories\UserAgentStringRepo;
use App\Repositories\ContentServerStatsRawRepo;
use App\Services\ServiceTraits\IdentifyUserAgent;
use App\Services\AbstractEtlService;

class UserAgentProcessingService extends AbstractEtlService {
    use IdentifyUserAgent;

    public function __construct(ContentServerStatsRawRepo $sourceRepo, UserAgentStringRepo $uaRepo) {
        parent::__construct($sourceRepo, $uaRepo);
        $this->setAgent();
    }

    public function extract($lookback) {
        $lookback = $lookback ? $lookback : config('jobs.uas.lookback');
        $this->data = $this->sourceRepo->pullUserAgents($lookback);
    }

    public function load() {
        foreach ($this->data->cursor() as $row) {
            $row = $this->transform($row);
            $this->targetRepo->loadData($row);
        }
    }

    protected function transform($row) {
        $uas = $row['user_agent'];
        
        return [
            'user_agent_string' => $uas,
            'browser' => $this->assignToBrowser($uas),
            'device' => $this->assignDeviceToFamily($uas),
            'is_mobile' => $this->isMobile($uas)
        ];
    }

}