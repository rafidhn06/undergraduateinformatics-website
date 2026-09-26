<?php

namespace Database\Seeders;

use App\Models\PasswordRecovery;
use App\Models\User;
use Illuminate\Database\Seeder;

class PasswordRecoverySeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'bif@telkomuniversity.ac.id')->value('id');

        if ($userId === null) {
            return;
        }

        PasswordRecovery::firstOrCreate(
            ['user_id' => $userId],
            [
                'first_question' => 'Pertanyaan pertama adalah?',
                'second_question' => 'Pertanyaan kedua adalah?',
                'first_answer' => 'jawaban satu',
                'second_answer' => 'jawaban dua',
            ]
        );
    }
}