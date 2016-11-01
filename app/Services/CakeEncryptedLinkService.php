<?php

namespace App\Services;

use App\Repositories\CakeEncryptedLinkRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class CakeEncryptedLinkService {
    
    private $linkRepo;

    public function __construct(CakeEncryptedLinkRepo $linkRepo) {
        $this->linkRepo = $linkRepo;
    }

    public function encryptCakeLink($link) {
        if ('' === $link) {
            return '';
        }

        $contents = parse_url($link);
        $params = $this->parseQueryParameters($contents['query']);

        $affiliateId = $params['a'] ?: '';
        $creativeId = $params['c'] ?: '';

        // this will throw an exception if nothing found:
        try {
            $encryptHash = $this->linkRepo->getHash($affiliateId, $creativeId);
        } catch (ModelNotFoundException $e){
            throw $e;
        }

        $url = $contents['scheme'] . '://' . $contents['host'] . $contents['path'] . '?' . $encryptHash;
        foreach ($params as $token => $value) {
            if ('a' !== $token && 'c' !== $token) {
                $url .= '&' . $token . '=' . $value;
            }
        }

        return $url;
    }

    public function fullEncryptLink($link) {
        if (!preg_match('/\&encmtfull/', $link)) {
            $link .= '&encmtfull';
        }
        return $link;
    }

    private function parseQueryParameters($query) {
        $pairs = explode('&', $query);
        $args = [];

        foreach($pairs as $str) {
            $tmp = explode('=', $str);
            $args[$tmp[0]] = $tmp[1];
        }

        return $args;
    }
}