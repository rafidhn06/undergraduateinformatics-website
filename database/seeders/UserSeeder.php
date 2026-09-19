<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'bif@telkomuniversity.ac.id'],
            [
                'password_recovery_id' => 1,
                'password' => 'akunadmin',
            ]
        );
    }
}