<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'linhvo0603@gmail.com'],
            [
                'name' => 'Linh Vo',
                'password' => Hash::make('06032002'),
            ]
        );
    }
}
