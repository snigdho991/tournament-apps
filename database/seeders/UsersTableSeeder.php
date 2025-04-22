<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::create([
            'name'     => 'Administrator',
    		'email'    => 'admin@example.com',
    		'password' => bcrypt('password'),
            'role'     => 'Administrator',
    	]); 

        $user->assignRole('Administrator');
        
    }
}
