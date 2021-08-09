<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {

        DB::table('users')->insert([
            'name' => 'Jiten',
            'email' => 'jitenbasnet7@gmail.com',
            'password' => bcrypt('9844604448'), // password
            'remember_token' => Str::random(10),
            'created_at'=>'2020-12-21 15:46:52',
            'updated_at'=>'2020-12-21 15:46:52',
            // 'password' => Hash::make($request['password']),
            'token' => Str::orderedUuid(),
        ]);


    }

}
