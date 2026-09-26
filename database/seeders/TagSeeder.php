<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'S1 Informatika', 'slug' => 's1-informatika', 'description' => 'Deskripsi tag S1 Informatika'],
            ['name' => 'Tugas Akhir', 'slug' => 'tugas-akhir', 'description' => 'Semua Informasi Mengenai Tugas Akhir'],
            ['name' => 'Registrasi Semester', 'slug' => 'registrasi-semester', 'description' => 'Berisi informasi terkait registrasi semester di Prodi S1 Informatika'],
            ['name' => 'IuM', 'slug' => 'ium', 'description' => 'MK Informatika untuk Masyarakat'],
            ['name' => 'MBKM', 'slug' => 'mbkm', 'description' => 'Berisi informasi tentang administrasi MBKM di Prodi S1 Informatika'],
            ['name' => 'Sidang', 'slug' => 'sidang', 'description' => 'tag untuk sidang tugas akhir dan yudisium'],
            ['name' => 'Perkuliahan', 'slug' => 'perkuliahan', 'description' => 'Informasi terkait pelaksanaan perkuliahan'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                ['name' => $tagData['name'], 'description' => $tagData['description']]
            );
        }
    }
}