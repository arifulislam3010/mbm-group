<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Finance\Balance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use DB;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('balances')->truncate();
        $dt = Carbon::now();
        $dateNow = $dt->toDateTimeString();

        $faker = Faker::create();
        for ($i=0; $i < 20; $i++) {
            Balance::create([
                'total_earn'  => mt_rand(5,150000000000000),
                'withdrawn'  => mt_rand(3,1000000000),
                'availabe_withdrawn'  => mt_rand(2,50000),
                'pending_clearance'  => mt_rand(2,5000),
            ]);

        }
    }
}