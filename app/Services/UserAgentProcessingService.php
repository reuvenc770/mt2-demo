<?php

namespace App\Services;
use App\Repositories\UserAgentStringRepo;
use App\Repositories\TrackingRepo;
use Jenssegers\Agent\Agent;
use App\Services\AbstractEtlService;

class UserAgentProcessingService extends AbstractEtlService {
    private $agent;

    public function __construct(TrackingRepo $sourceRepo, UserAgentStringRepo $uaRepo) {
        parent::__construct($sourceRepo, $uaRepo);
        $this->agent = new Agent();
    }

    public function extract($lookback) {
        $lookback = $lookback ? $lookback : config('jobs.uas.lookback');
        $this->data = $this->sourceRepo->pullUserAgents($lookback);
    }

    protected function transform($row) {
        $uas = $row['user_agent_string'];
        $this->agent->setUserAgent($uas);

        return [
            'user_agent_string' => $uas,
            'browser' => $this->assignToBrowser($uas),
            'device' => $this->assignDeviceToFamily($uas),
            'is_mobile' => $this->agent->isMobile()
        ];
    }

    private function assignDeviceToFamily($uas) {
        
        $device = $this->agent->device();
        $os = $this->agent->platform();

        if ('iPhone' === $device && 'iOS' === $os) {
            return 'iPhone';
        }
        elseif ('iPad' === $device && 'iOS' === $os) {
            return 'iPad';
        }
        elseif ('AndroidOS' === $os && 'iPhone' === $device && preg_match('/Windows/', $uas)) {
            // An odd rule for a strange diagnosis by the library
            return 'Windows Phone';
        }
        elseif ('AndroidOS' === $os) {
            return 'Android';
        }
        elseif ('Windows' === $os || 'OS X' === $os || preg_match('/X11/', $uas)) {
            return 'Desktop';
        }
        elseif (preg_match('/Windows\sPhone/', $uas)) {
            return 'Windows Phone';
        }
        elseif (preg_match('/BB10/', $uas) || preg_match('/PlayBook/', $uas) || preg_match('/BlackBerry/', $uas)) {
            return 'Blackberry';
        }
        else {
            return 'Misc';
        }
    }

    private function assignToBrowser($uas) {
        $browser = $this->agent->browser();

        if ('Safari' === $browser && preg_match('/IEMobile/', $uas)) {
            return 'IE Mobile';
        }
        else {
            return $browser;
        }
    }
}