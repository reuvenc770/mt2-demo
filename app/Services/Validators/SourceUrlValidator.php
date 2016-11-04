<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use App\Repositories\FeedRepo;

class SourceUrlValidator implements IValidate {

    private $sourceUrl;
    private $feedId;
    private $feedRepo;

    public function __construct(FeedRepo $feedRepo) {
        $this->feedRepo = $feedRepo;
    }

    public function getRequiredData() {
        return ['source_url', 'feed_id'];
    }

    public function setData($data) {
        $this->sourceUrl = $data['source_url'];
        $this->feedId = $data['feed_id'];
    }


    public function validate() {
        if ('' === $this->sourceUrl) {
            $feedDefaultSourceUrl = $this->feedRepo->getSourceUrl($this->feedId);

            if ('' === $feedDefaultSourceUrl || null == $feedDefaultSourceUrl) {
                throw new ValidationException("No valid source url found in import or in feed");
            }
            else {
                $this->sourceUrl = $feedDefaultSourceUrl;
            }
        }

        if (preg_match('/betheboss/', $this->sourceUrl)) {
            throw new ValidationException("Source url invalid - contains betheboss: {$this->sourceUrl}");
        }

        $urlForParsing = preg_match('/^http:\/\//', $this->sourceUrl) ? $this->sourceUrl : 'http://' . $this->sourceUrl;
        $parsed = parse_url($urlForParsing);

        if (preg_match('/\.ca$/', $parsed['host'])) {
            throw new ValidationException("Source url invalid - has Canadian domain: {$this->sourceUrl}");
        }

        // Set to just the domain
        $this->sourceUrl = $parsed['host'];
    }


    public function returnData() {
        return [
            'source_url' => $this->sourceUrl,
            'feed_id' => $this->feedId
        ];
    }

}