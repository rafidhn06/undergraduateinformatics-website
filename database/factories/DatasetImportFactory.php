<?php

namespace Database\Factories;

use App\Models\DatasetImport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DatasetImportFactory extends Factory
{
    protected $model = DatasetImport::class;

    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'token' => Str::random(48),
            'status' => 'staged',
            'payload' => ['datasets' => []],
            'expires_at' => now()->addHour(),
        ];
    }
}
