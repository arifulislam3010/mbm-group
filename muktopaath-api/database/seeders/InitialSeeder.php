<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Myaccount\User;
use App\Models\Myaccount\UserInfo;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Myaccount\SystemRole;
use Illuminate\Support\Facades\Hash;
use DB;
use Yaml;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Yaml::parse(file_get_contents(resource_path('yaml/role/role.yaml')));

        $access = [];

        foreach ($role['admin']['service'] as $key => $value) {
            foreach ($value['access'] as $key1 => $value1) {
                $access[$key1] = $value1;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('institution_infos')->truncate();
        DB::table('users')->truncate();
        DB::table('user_infos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = '12345678';
        User::create([
                'name' => 'Super admin',
                'username'  => 'muktopaathAdmin',
                'type' =>  1,
                'status' => 1,
                'phone'     => '',
                'email'     => 'admin@gmail.com',
                'verify_status' => 1,
                'password' => Hash::make($password),

            ]);

        UserInfo::create([
            'created_by' => 1,
            'user_id'  => 1,

        ]);

        InstitutionInfo::create([
            'institution_name' => 'মুক্তপাঠ',
            'user_id'  => 1,
            'institution_type_id'  => 1,
            'username'  => 'muktopaath',
            'address'  => 'Dhaka',
            'status'  => 1,
            'created_by'  => 1,

        ]);

        SystemRole::create([
            'role' => 'super',
            'json_schema'  => json_encode($role['admin']['service']),
            'access'  => json_encode($access),
            'owner_id'  => 1,
            'user_id'  => 1,
            'created_by'  => 1,

        ]);

    }
}
