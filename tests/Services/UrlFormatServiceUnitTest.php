<?php

namespace Tests\Services;

use Tests\TestCase;

class UrlFormatServiceUnitTest extends TestCase {

    public $sut;
    public $contentDomain;
    public $emailidField;
    public $linkId;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Services\UrlFormatService();

        $this->contentDomain = 'test.com';
        $this->emailIdField = "{emailId}";
        $this->linkId = 1;
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    /**
     *  These require randomized values 
     *  Building a poor man's fuzzer
     */

    public function test_formatNewUrl_Redirect() {
        $type = 'REDIRECT';

        for ($i = 0; $i < 100; $i++) {
            $url = $this->sut->formatNewUrl($type, $this->contentDomain, $this->emailIdField, $this->linkId);
            $matchPattern = "/http:\/\/test\.com\/z\/[a-z0-9]{6,15}\/\{emailId\}\|1\|1\|R/";

            if (!preg_match($matchPattern, $url)) {
                $this->assertTrue(false);
            }
        }
        
        $this->assertTrue(true);
    }

    public function test_formatNewUrl_AdvUnsub() {
        $type = 'ADVUNSUB';

        for ($i = 0; $i < 100; $i++) {
            $url = $this->sut->formatNewUrl($type, $this->contentDomain, $this->emailIdField, $this->linkId);
            $matchPattern = "/http:\/\/test\.com\/z\/[a-z0-9]{6,15}\/\{emailId\}\|1\|1\|A/";

            if (!preg_match($matchPattern, $url)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    public function test_formatGmailUrl_Redirect() {
        $type = 'REDIRECT';

        for ($i = 0; $i < 100; $i++) {
            $url = $this->sut->formatGmailUrl($type, $this->contentDomain, $this->emailIdField, $this->linkId);
            $matchPattern = '/http:\/\/test.com\/[A-Z0-9][A-Za-z0-9]{3}\/[a-z0-9]{6,15}\/\{emailId\}\|[a-z0-9]{6,15}\|1\|\d{1,6}/';

            if (!preg_match($matchPattern, $url)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    public function test_formatGmailUrl_AdvUnsub() {
        $type = 'ADVUNSUB';

        for ($i = 0; $i < 100; $i++) {
            $url = $this->sut->formatGmailUrl($type, $this->contentDomain, $this->emailIdField, $this->linkId);
            $matchPattern = '/http:\/\/test.com\/[A-Z0-9][A-Za-z0-9]{3}\/[a-z0-9]{6,15}\/\{emailId\}\|[a-z0-9]{6,15}\|1\|[A-Za-z]{4,8}/';

            if (!preg_match($matchPattern, $url)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    public function test_getDefinedRandomString() {
        // closest we can get to directly testing randomString()

        for ($i = 0; $i < 100; $i++) {
            $randomString = $this->sut->getDefinedRandomString();

            if (!preg_match('/[A-Za-z0-9]{6,15}/', $randomString)) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }
}