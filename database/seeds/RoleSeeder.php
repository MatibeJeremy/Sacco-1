<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // admin role
        $admin = [
            'created_at' => Carbon::now(),
            'name' => 'ADMIN',
            'description' => 'User with admin privileges.',
            'updated_at' => Carbon::now()
        ];
        // client role
        $client = [
            'created_at' => Carbon::now(),
            'name' => 'CLIENT',
            'description' => 'Standard client user.',
            'updated_at' => Carbon::now()
        ];

        DB::table('roles')->insert([
            $admin,
            $client
        ]);
    }
}
