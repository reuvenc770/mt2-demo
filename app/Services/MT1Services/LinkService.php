<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\LinkRepo;
use App\Repositories\LinkRepo as CmpLinkRepo;
use Exception;
use Guzzle;

class LinkService {
    
    private $repo;
    private $cmpRepo;

    public function __construct(LinkRepo $repo, CmpLinkRepo $cmpRepo) {
        $this->repo = $repo;
        $this->cmpRepo = $cmpRepo;
    }

    /**
     *  Checks link validity
     *  If the redirects fail, throw an exception
     *  If the old cookie domain servegent.com appears, return false
     *  Otherwise, return true
     */

    public function checkLink($link) {
        $result = Guzzle::get($link, [
            'allow_redirects' => [
                    'max' => 500,
                    'strict' => false,
                    'referrer' => false,
                    'protocols' => ['http', 'https'],
                    'track_redirects' => true
                ],
        ]);

        if (404 === (int)$result->getStatusCode()) {
            throw new Exception("Link $link is a 404");
        }

        $body = (string)$result->getBody();

        if (strpos($body, 'servegent.com')) {
            return false;
        }

        return true;
    }


    public function getLinkId($url) {
        $linkId = $this->repo->getLinkId($url);
        $this->cmpRepo->updateOrCreate([
            'id' => $linkId,
            'url' => $url
        ]);
        return $linkId;
    }

}