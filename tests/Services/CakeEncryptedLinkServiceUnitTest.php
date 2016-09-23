<?php

namespace Tests\Services;

use Tests\TestCase;
use App;
use \App\Repositories\CakeEncryptedLinkRepo;
#use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class CakeEncryptedLinkServiceUnitTest extends TestCase {

    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $link = factory( \App\Models\CakeEncryptedLink::class )->create();
        $linkRepo = new CakeEncryptedLinkRepo($link);
        $this->sut = new \App\Services\CakeEncryptedLinkService($linkRepo);
    }

    public function tearDown () {
        unset( $this->sut );
        parent::tearDown();
    }

    /**
     *  @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_encryptCakeLink_empty() {
        $url = 'http://www.mydomain.com/test/path/?a=0&c=0&s1=1392329';
        $cakeEncryptedUrl = $this->sut->encryptCakeLink($url);
    }

    public function test_encryptCakeLink_nonEmpty() {
        $url = 'http://www.mydomain.com/test/path/?a=309&c=10&s1=1392329';
        $cakeEncryptedUrl = $this->sut->encryptCakeLink($url);

        $targetUrl = 'http://www.mydomain.com/test/path/?lnwk=Yabp3hZ%2BGXGlJUmf7odYFQ%3D%3D&s1=1392329';
        
        $this->assertTrue($cakeEncryptedUrl === $targetUrl);
    }

    public function test_fulEncryptLink_includes() {
        $url = 'http://test.com/cgi-bin/test.php?s=1&encmtfull';
        $url2 = $this->sut->fullEncryptLink($url);
        $this->assertTrue($url === $url2);
    }

    public function test_fulEncryptLink_doesNotInclude() {
        $url = 'http://test.com/cgi-bin/test.php?s=1';
        $url2 = $this->sut->fullEncryptLink($url);
        $this->assertTrue($url2 === ($url . '&encmtfull') );
    }

}