<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DatabaseTest extends TestCase
{
    use DatabaseTransactions;

    public function testDatabase()
    {
        // Make call to application...
        $this->seeInDatabase('users', ['email' => 'adm@teste.com']);
    }


}
