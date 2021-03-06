<?php

namespace App\Services;

use App\Exceptions\UrlValidationException;

class Url {
    
    private $url;

    private $protocol;
    private $host;
    private $fileName;
    private $query;
    private $directoryPath;
    private $mimeType = '';
    private $queryValues;

    public function __construct($url) {
        $this->url = $url;
        $this->parseUrl();
    }

    private function parseUrl() {
        $parsed = parse_url($this->url);

        if ($parsed === false) {
            throw new UrlValidationException($this->url);
        }

        if ('#' === $this->url || (isset($parsed['path']) && $this->url === $parsed['path']) || (array_keys($parsed) === ['fragment'])) {
            // We have a url with no scheme
            // Likely not a real url
            // Can be a token like {{ADV_UNSUB_URL}} or '#anchor'
            $this->protocol = '';
            $this->host = '';
            $this->query = '';
            $this->queryValues = [];
            $this->directoryPath = '';
            $this->fileName = '';
        }
        elseif ('tel' === $parsed['scheme']) {
            // we have a telephone number
            $this->protocol = $parsed['scheme'];
            $this->host = $parsed['path'];
            $this->query = '';
            $this->queryValues = [];
            $this->directoryPath = '';
            $this->fileName = '';
        }
        elseif ('mailto' === $parsed['scheme']) {
            $this->protocol = $parsed['scheme'];
            $this->host = $parsed['path'];
            $this->query = $parsed['query'];
            $this->queryValues = [];
            $this->directoryPath = '';
            $this->fileName = '';
        }
        else {
            $splitPath = $this->splitPath($parsed['path']);
            $this->protocol = $parsed['scheme'];
            $this->host = $parsed['host'];
            $this->query = isset($parsed['query']) ? $parsed['query'] : '';
            $this->directoryPath = $splitPath[0];
            $this->fileName = $splitPath[1];
            $this->queryValues = $this->parseQueryParameters($this->query); 
        }
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
        // this is the check Link method
    }

    public function stringReplace($from, $to) {
        $this->url = str_replace($from, $to, $this->url);
        $this->parseUrl(); // re-parse url 
    }

    public function regexReplace($from, $to) {
        $this->url = preg_replace($from, $to, $this->url);
        $this->parseUrl();
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

    public function getQueryParam($param) {
        if (isset($this->queryValues[$param])) {
            return $this->queryValues[$param];
        }
        else {
            throw new UrlValidationException("URL {$this->url} missing parameter {$param}. Please check creative.");
        }
    }

    public function contains($substr) {
        return substr_count($this->url, $substr) > 0;
    }

    private function parseQueryParameters($query) {

        if ('' === $query) {
            return [];
        }
        
        $pairs = explode('&', $query);
        $args = [];

        foreach($pairs as $str) {
            $tmp = explode('=', $str);
            $args[$tmp[0]] = $tmp[1];
        }

        return $args;
    }

    private function executeCurl() {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $this->result = curl_exec($ch);
        $this->mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
    }
}

