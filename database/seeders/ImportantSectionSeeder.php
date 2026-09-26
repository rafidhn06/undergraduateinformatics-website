<?php

namespace Database\Seeders;

use App\Models\ImportantSection;
use Illuminate\Database\Seeder;

class ImportantSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'General', 'order_number' => 1],
            ['name' => 'Layanan Prodi S1 Informatika', 'order_number' => 2],
            ['name' => 'Info Kemahasiswaan dan TAK', 'order_number' => 3],
            ['name' => 'Kurikulum 2024', 'order_number' => 4],
            ['name' => 'Registrasi S-1 IF Semester Ganjil TA 2026/2027', 'order_number' => 5],
            ['name' => 'Informatika untuk Masyarakat', 'order_number' => 6],
            ['name' => 'Sidang TA dan Yudisium', 'order_number' => 7],
            ['name' => 'Kerja Praktik S-1 IF', 'order_number' => 8],
            ['name' => 'Magang Berdampak 2025', 'order_number' => 9],
            ['name' => 'Proposal dan Tugas Akhir S-1 IF', 'order_number' => 10],
            ['name' => 'Registrasi S-1 IF Semester Genap TA 2025/26', 'order_number' => 11],
            ['name' => 'HMS 2017 dan 2018 (Expired)', 'order_number' => 12],
        ];

        foreach ($sections as $sectionData) {
            ImportantSection::firstOrCreate(
                ['name' => $sectionData['name']],
                ['order_number' => $sectionData['order_number']]
            );
        }
    }
}