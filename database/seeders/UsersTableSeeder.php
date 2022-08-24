<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'name'     => 'Clecyo Ferreira', 
            'email'    => 'carlos@c7tech.com.br',
            'password' => bcrypt('123456'),
        ]);
    }
}
