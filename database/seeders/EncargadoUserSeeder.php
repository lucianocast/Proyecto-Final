<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class EncargadoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'encargado@example.test'],
            [
                'name' => 'Usuario Encargado',
                'password' => bcrypt('password'),
                'role' => 'encargado',
            ]
        );
    }
}
