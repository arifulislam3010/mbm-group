<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class MuktopaathTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get("admin-settings/categories");
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                [
                    'id',
                    'title',
                    'bn_title'
                ]
            ]    
        );
    }
}
