<?php

namespace App\Repositories\RepoTraits;
use DB;

// A trait for working with IPv6 addresses

trait IPv6 {
    private function validateIp($ip) {
        $validatedIp = filter_var($ip, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV6]);

        if (false === $validatedIp) {
            throw new \Exception("$ip is not an ipv6 email address");
        }

        return $validatedIp;
    }

    private function splitIp($ip) {
        // received a validated ipv6 address
        $fullIp = $this->generateFullIp($ip);

        $words = explode(':', $fullIp);

        return [
            '::' . $words[0] . ':' . $words[1] . ':' . $words[2] . ':' . $words[3],
            '::' . $words[4] . ':' . $words[5] . ':' . $words[6] . ':' . $words[7]
        ];

    }

    private function generateFullIp($ip) {
        // receives a validated ipv6 address
        // Need to generate full ip,
        // 2801:80:1b90:: is a likely start
        // but ::192:168:0:1 is possible too
        // as is 2801:80:1b90::ffff

        $count = $this->getWordCount($ip);

        if (8 === $count) {
            // full ip already
            return $ip;
        }
        else {
            // condensed
            $replacements = [];
            $i = 1;
            
            while ($i <= 8 - $count) {
                $replacements[] = '0';
                $i++;
            }

            $replaceStr = implode(':', $replacements);
            $position = strpos($ip, '::');

            $finalIp = '';

            if ((strlen($ip) - 2) === $position) {
                // at the end
                // remove last colon and replace
                $finalIp = preg_replace('/\:$/', $replaceStr, $ip);
            }
            elseif (0 === $position) {
                // at the beginning
                // remove first colon and replace
                $finalIp = preg_replace('/^\:/', $replaceStr, $ip);
            }
            elseif (false === $position) {
                throw new \Exception("Unexpected ipv6 format: $ip");
            }
            else {
                // somewhere in the middle
                $split = explode('::', $ip);
                $finalIp = $split[0] . ':' . $replaceStr . ':' . $split[1];
            }

            return $finalIp;
        }
    }


    private function getWordCount($ip) {
        // assumes a validated ipv6 address
        $count = 0;
        $words = explode(':', $ip);

        foreach ($words as $word) {
            if ('' !== $word) {
                $count++;
            }
        }

        return $count;
    }
}