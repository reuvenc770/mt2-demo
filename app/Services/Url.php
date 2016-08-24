<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use GuzzleHttp\Client;

class Url {
    
    private $url;

    private $protocol;
    private $host;
    private $fileName;
    private $query;
    private $directoryPath;
    private $mimeType = '';

    public function __construct($url) {

        $parsed = parse_url($url);

        if ($parsed === false) {
            throw new ValidationException("URL $url is not valid.");
        }
        $this->url = $url;
        $splitPath = $this->splitPath($parsed['path']);

        $this->protocol = $parsed['scheme'];
        $this->host = $parsed['host'];
        $this->query = $parsed['query'];
        $this->directoryPath = $splitPath[0];
        $this->fileName = $splitPath[1];

    }


    private function splitPath($path) {
        // negative lookahead regex - splits on the last '/'
        // so between the directories and the file name
        return preg_split('/(\/)(?!.*\/)/', $path);
    }

    public function __get($prop) {
        return $this->$prop ?: '';
    }

    public function checkUrl() {
        // this is the check Link methodology
    }

    public function stringReplace($from, $to) {
        $this->url = str_replace($from, $to, $this->url);
    }

    public function regexReplace($from, $to) {
        $this->url = preg_replace($from, $to, $this->url);
    }

    public function getContents() {
        if (!$this->result) {
            $this->executeCurl();
        }

        return $this->result;
    }

    public function getMimeType() {
        if ('' === $this->mimeType) {
            $this->executeCurl();
        }

        return $this->mimeType;
    }

    private function executeCurl() {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $this->result = curl_exec($ch);
        $this->mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
    }
}

