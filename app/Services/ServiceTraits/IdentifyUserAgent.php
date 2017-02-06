<?php

namespace App\Services\ServiceTraits;

use Jenssegers\Agent\Agent;

trait IdentifyUserAgent {
    private $agent;

    private function setAgent() {
        $this->agent = new Agent();
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
        elseif (preg_match('/Pixel/', $uas) && 'AndroidOS' === $os) {
            return 'Google Pixel Android';
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

    private function getDeviceType($uas) {
        $this->agent->setUserAgent($uas);
        return $this->agent->isMobile() ? 'Mobile' : 'Desktop';
    }

    private function isMobile($uas) {
        $this->agent->setUserAgent($uas);
        return $this->agent->isMobile();
    }
}