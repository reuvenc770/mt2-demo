<?php

namespace Tests\Acceptance;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class SimpleTest extends TestCase
{

    public function testSimple()
    {

        $this->assertTrue(true);

    }

}
