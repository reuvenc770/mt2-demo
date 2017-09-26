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
        return ['sourceUrl', 'feedId'];
    }

    public function setData(array $data) {
        $this->sourceUrl = $data['sourceUrl'];
        $this->feedId = $data['feedId'];
    }


    public function validate() {
        if ('' === $this->sourceUrl) {
            $feedDefaultSourceUrl = $this->feedRepo->getSourceUrl($this->feedId);

            if ('' === $feedDefaultSourceUrl || null == $feedDefaultSourceUrl) {
                $feedName = $this->feedRepo->fetch($this->feedId)->name;
                $this->sourceUrl = 'na.' . $feedName . '.com';
            }
            else {
                $this->sourceUrl = $feedDefaultSourceUrl;
            }
        }

        if (preg_match('/betheboss/', $this->sourceUrl)) {
            throw new ValidationException("Source url invalid - contains betheboss: {$this->sourceUrl}");
        }

        $parsed = [];

        if (preg_match('/^http[s]*\:\/\//i', $this->sourceUrl)) {
            $parsed = parse_url($this->sourceUrl);
        }
        elseif (preg_match('/^https\/\//i', $this->sourceUrl)) {
            $urlForParsing = str_replace('https', 'https:', $this->sourceUrl);
            $parsed = parse_url($urlForParsing);
        }
        elseif (preg_match('/^http\/\//i', $this->sourceUrl)) {
            // a common error
            $urlForParsing = str_replace('http', 'http:', $this->sourceUrl);
            $parsed = parse_url($urlForParsing);
        }
        else {
            // No protocol (at least based off off what we've seen)
            $parsed = parse_url('http://' . $this->sourceUrl);
        }

        if (preg_match('/\.ca$/', $parsed['host'])) {
            throw new ValidationException("Source url invalid - has Canadian domain: {$this->sourceUrl}");
        }

        // Set to just the domain
        $this->sourceUrl = $parsed['host'];
    }


    public function returnData() {
        return [
            'sourceUrl' => $this->sourceUrl,
            'feedId' => $this->feedId
        ];
    }

}