<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventManager\Event;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use DB;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->truncate();
        $dt = Carbon::now();
        $dateNow = $dt->toDateTimeString();

        $faker = Faker::create();
        for ($i=0; $i < 20; $i++) {
            Event::create([
                'title' => $faker->name,
                'type'  => mt_rand(1,3),
                'details' =>  $faker->realText(500, 2),
                'thumbnail' => $faker->imageUrl($width = 640, $height = 480),
                'start_time' => $dateNow,
                'end_time' => $dateNow,
                // 'password' => Hash::make('password'),

            ]);

        }
    }
}