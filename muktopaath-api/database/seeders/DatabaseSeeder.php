<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EventManager\EventSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call([
            EventSeeder::class,
        ]);
    }
}