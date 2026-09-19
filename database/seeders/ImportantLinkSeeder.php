<?php

namespace Database\Seeders;

use App\Models\ImportantLink;
use App\Models\ImportantSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ImportantLinkSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'name' => 'General',
                'links' => [
                    ['name' => 'Video Profil Prodi S-1 Informatika', 'link' => 'https://www.youtube.com/watch?v=lLXiWB22rfU'],
                    ['name' => 'Telkom University One Stop Service', 'link' => 'https://toss.telkomuniversity.ac.id/'],
                    ['name' => 'Pengajuan Tanda Tangan Kaprodi S1 Informatika', 'link' => 'https://forms.office.com/pages/responsepage.aspx?id=D_6vkKPCCEG7mGzrTpTvFc9ujqZdH91MtXpfw-rWy2hUNkJYOVVGR0NGMFYxS1Q1SFZKQ0NLNVlQWS4u'],
                    ['name' => 'Link Tree LAAK FIF', 'link' => 'https://linktr.ee/laaksoc'],
                    ['name' => 'Group Angkatan', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQDnurkY_8uAR4FUsAFD1N8qAfAQoytP3MqcE5VbOUnI14E?e=6qih5l'],
                    ['name' => 'General Information for IF INT', 'link' => 'http://bit.ly/bif-intinfo'],
                    ['name' => 'Daftar Layanan di Universitas Telkom', 'link' => 'https://servicedesk.telkomuniversity.ac.id/daftar-kontak-layanan-tel-u/'],
                ],
            ],
            [
                'name' => 'Layanan Prodi S1 Informatika',
                'links' => [
                    ['name' => 'Form Layanan Pengaduan dan Aspirasi Mahasiswa dan Dosen di Prodi S-1 Informatika', 'link' => 'https://forms.office.com/r/cZuHFE5E3Z'],
                    ['name' => 'Form Agenda Pertemuan Publik (Orang Tua/Wali/Mahasiswa) Dengan Prodi', 'link' => 'https://forms.office.com/r/eZNpGdMXcb'],
                ],
            ],
            [
                'name' => 'Info Kemahasiswaan dan TAK',
                'links' => [
                    ['name' => 'Slide Sosialisasi TAK 2025', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQAy9fvsYBFaSLIMfx91JNeZAXCwlT3W6YBaTvCS4klQ41M?e=XLKfdb'],
                    ['name' => 'Link Tree DitMawa Universitas Telkom', 'link' => 'https://linktr.ee/ditmawa_univtelkom'],
                    ['name' => '2526-2 Rekaman Sosialisasi TAK 2025', 'link' => 'https://youtu.be/r0EBNnKfzyA'],
                ],
            ],
            [
                'name' => 'Kurikulum 2024',
                'links' => [
                    ['name' => 'Silabus/RPS Mata Kuliah S-1 Informatika Kurikulum 2020 (Umum)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:f:/g/personal/bif_telkomuniversity_ac_id/IgDPpwZuiF7cTIXEtZ-DjX01AUVdwVzkm-16sWW5LCCF-Fs?e=YDKgpR'],
                    ['name' => 'Silabus/RPS Mata Kuliah S-1 Informatika', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/bif_telkomuniversity_ac_id/IQDEahOTaz4pTK9M4viWOHMnAX31P8FrbpMocdZHyB6A87E?e=2OazAF'],
                    ['name' => 'Katalog Mata Kuliah S-1 IF Kurikulum 2024', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQBtmu2az3qPRbT6z3DnPiqnAZVxIAuY3AFMBDxbb8lbnj4?e=kbAM2U'],
                ],
            ],
            [
                'name' => 'Registrasi S-1 IF Semester Ganjil TA 2026/2027',
                'links' => [
                    ['name' => '2627-1 Video Sosialisasi Registrasi [Mahasiswa]', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:v:/g/personal/bif_telkomuniversity_ac_id/IQC9VUh3VW7JQ4_9koy0YP-cAU7mrROwLlruclQsjbWs3Sg?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJTdHJlYW1XZWJBcHAiLCJyZWZlcnJhbFZpZXciOiJTaGFyZURpYWxvZy1MaW5rIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXcifX0%3D&e=yXzpQB'],
                    ['name' => '2627-1 Slide materi sosialisasi registrasi [Mahasiswa]', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:p:/g/personal/bif_telkomuniversity_ac_id/IQDXtGVLUS8ASpNGal5RQmG2AVSQ0gZfVRMpcWtvHBoch3M?e=o47Wyy'],
                    ['name' => '2627-1 Rencana Jadwal Perkuliahan Mahasiswa', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQBuaJKY1Y6PToq4D105860mAVCrujBVyMLyLoc_LzQ9iQU?e=YCyv5E'],
                    ['name' => '2627-1 Excel Rencana Studi Setiap Angkatan', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQC8QOIIRtTDR7By9UfvuooMAbzEEZSPckX3gQM5p2peSa0?e=uFxA7g'],
                ],
            ],
            [
                'name' => 'Informatika untuk Masyarakat',
                'links' => [
                    ['name' => 'Timeline IuM Genap 2025/26 (Update 9 Juni 2026)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQCp_o9xgi56QpCxa3C8MxwkAUz-eo4rU7Rdy3jG5AVihJ8?e=c3ccht'],
                    ['name' => 'Template Surat Pengantar (Opsional apabila mitra meminta, TTD KaProdi ajukan via form pengajuan TTD KaProdi)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/bif_telkomuniversity_ac_id/IQCmqBTAJpYNTL6IlitajrILAcihOF59cse0AGD3mLsVdTo?e=VKS9eX'],
                    ['name' => 'Template Proposal & Laporan Akhir IUM 2026', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:f:/g/personal/bif_telkomuniversity_ac_id/IgDdF8KO6I4QQZX_KwUAa5q6AT43jEa_Uf54VQGnNOl1ux0?e=TzqF6Y'],
                    ['name' => 'Roadmap Abdimas Kelompok Keahlian FIF 2025-2029', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQA6g3PtGXHzT7XRmV5pRU8DAR0rIbtxS1FPUj5BZOSU7ZU?e=EpjqrQ'],
                    ['name' => 'Rekaman Sosialisasi IuM Genap 2025/26', 'link' => 'https://youtu.be/tJWpQy3V66c'],
                    ['name' => 'Panduan IuM 2026 (Wajib dibaca)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQA86i4joMnhSYGWUkEnljJhAdGHJOWoR6-Q1apyK0Bd9QA?e=qwBHhr'],
                    ['name' => 'Form Pengajuan Rekognisi IUM', 'link' => 'https://forms.office.com/r/cJKr2QdLYu'],
                ],
            ],
            [
                'name' => 'Sidang TA dan Yudisium',
                'links' => [
                    ['name' => 'Template LaTex Proposal dan Buku TA', 'link' => 'https://www.overleaf.com/latex/templates/template-proposal-tugas-akhir-fif-telkom-university-tel-u-phn/bcssdsqhdfyr'],
                    ['name' => 'Template LaTex Format Paper TA', 'link' => 'https://www.overleaf.com/read/pwfkshrvbwry#61464d'],
                    ['name' => 'Jenis Sidang TA dan Yudisium', 'link' => 'https://info-bif.telkomuniversity.ac.id/post/9'],
                    ['name' => 'Informasi Umum Pendafataran Sidang FIF', 'link' => 'https://bit.ly/FIFInformasiUmumPendaftaranSidang'],
                    ['name' => 'Form Pendaftaran Sidang Terjadwal', 'link' => 'http://bit.ly/DaftarSidangTerjadwal-S1IFV2'],
                    ['name' => 'Form Pendaftaran Sidang Reguler dan Seminar Pengganti Sidang', 'link' => 'http://bit.ly/FIFPendaftaranSidangV2'],
                ],
            ],
            [
                'name' => 'Kerja Praktik S-1 IF',
                'links' => [
                    ['name' => 'Slide Sosialisasi KP Tahun 2026', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQDuL7C9sodpSLJLNT_bhBd2AdvHrNRsj8piAePEDj9n9q8?e=UtX5zB'],
                    ['name' => 'Sistem Penilaian Kerja Praktek (SiKaPe)', 'link' => 'https://apps-soc.telkomuniversity.ac.id/'],
                    ['name' => 'Rekaman Sosialisasi KP Tahun 2026', 'link' => 'https://youtu.be/fLlRWS32AsA'],
                    ['name' => 'Rekaman Sosialisasi KP 2024', 'link' => 'https://youtu.be/q2M7YB15QA4'],
                    ['name' => 'Lampiran Berkas KP versi Ms. Word', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/bif_telkomuniversity_ac_id/EfqyUewUwuBLgl__9V-HrXMB9ps78N5rmAJ-Mq-bNYUvnQ?e=mTlrX5'],
                    ['name' => 'Form Pengajuan Dosen Pembimbing Akademik KP 2026', 'link' => 'https://forms.office.com/r/YpkgTji1B8'],
                    ['name' => 'Administrasi Kerja Praktik dari LAAK', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/laaksoc_365_telkomuniversity_ac_id/EUeKw3-vRNhJq5Ycaq7TBdUBXL2RO5izCeyhR0zBncuyEg?e=u7LXc2'],
                ],
            ],
            [
                'name' => 'Magang Berdampak 2025',
                'links' => [
                    ['name' => 'Rekaman Sosialisasi Magang Berdampak', 'link' => 'https://youtu.be/D9v6rseB3PM'],
                    ['name' => 'Panduan Magang Berdampak 2025 S-1 IF', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:f:/g/personal/bif_telkomuniversity_ac_id/Es28WcBjkoVBnjloZah0Tv0B0ExDwtC51G9b5OLad755nw?e=RFniCH'],
                    ['name' => 'Alur Pengajuan Konversi MBKM Genap 2526', 'link' => 'https://tel-u.ac.id/genap2526-infoajuankonversimks1if'],
                ],
            ],
            [
                'name' => 'Proposal dan Tugas Akhir S-1 IF',
                'links' => [
                    ['name' => 'Informasi Tugas Akhir Fakultas Informatika (Panduan, timeline, dan lainnya)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/laaksoc_365_telkomuniversity_ac_id/IQA0UTLq_KPUS6b6ZHnik56OASF2IOAl3zdgAcVrnFcOgXw?e=8yIgNO'],
                ],
            ],
            [
                'name' => 'Registrasi S-1 IF Semester Genap TA 2025/26',
                'links' => [
                    ['name' => '[Rekaman] Sosialisasi Registrasi Sesi Mahasiswa', 'link' => 'https://youtu.be/b_g07M6B2Hg'],
                    ['name' => '[Rekaman] Sosialisasi Registrasi Sesi Dosen', 'link' => 'https://youtu.be/rqasi_HhOzE'],
                    ['name' => '[FIF] Pengajuan Penundaan Pembayaran TEL-U CARE', 'link' => 'https://info-bif.telkomuniversity.ac.id/post/24'],
                    ['name' => '[FIF] Informasi Registrasi, Pembayran, Undur Diri, Cuti, Aktivasi Mahasiswa Semester Genap 2526', 'link' => 'https://info-bif.telkomuniversity.ac.id/post/23'],
                    ['name' => '[FIF] Informasi Jadwal Pembayaran dan Tata Cara Pembayaran pada Sistem Baru', 'link' => 'https://info-bif.telkomuniversity.ac.id/post/25'],
                    ['name' => 'SIRAMA', 'link' => 'https://sirama.telkomuniversity.ac.id/'],
                    ['name' => 'Buku Saku Kurikulum 2024', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:f:/g/personal/bif_telkomuniversity_ac_id/EjxcCp1sgDFDl0xqwbLIQbIBh6Odd3XuGRomxfQC7lyaJw?e=yRIRVZ'],
                    ['name' => '2526-2 Slide Topic Shopping Tugas Akhir dari Penelitian Dosen KK 2025', 'link' => 'https://tel-u.ac.id/materisosialisasitopiktaganjil2526'],
                    ['name' => '2526-2 Slide Sosialisasi Registrasi Prodi S-1 IF', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:b:/g/personal/bif_telkomuniversity_ac_id/IQCp0zf0Z0PzSK6hWDQblF6rASmbH8R56AbOM-D6mHyf9Cc?e=xn6dTz'],
                    ['name' => '2526-2 Rekaman Topic Shopping Tugas Akhir dari Penelitian Dosen KK 2025', 'link' => 'https://youtu.be/5sfmJtJ9UlA'],
                    ['name' => '2526-2 Pengumuman Registrasi BSLA', 'link' => 'https://baa.telkomuniversity.ac.id/pengumuman-registrasi-semester-genap-ta-2025-2026/'],
                    ['name' => '2526-2 Jadwal Mata Kuliah Genap S-1 IF', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQCU5FaiEhK8SJjqByQgXg9KAZjfKmZapnrctvTd6ZBYwQg?e=FxIxWh&nav=MTVfe0Q1ODZERTYwLTI1NDctNTM0Qi1CMjExLTI5MzRBQzY2MTExNH0'],
                    ['name' => '2526-2 Excel Rencana Studi Setiap Angkatan', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQBsCj9kaOkFSp3FI6vdjY47ATtUodDOoez9WPVRylm_5ps?e=iCmPtg'],
                    ['name' => '2526-2 Daftar Mata Kuliah', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQCU5FaiEhK8SJjqByQgXg9KAZjfKmZapnrctvTd6ZBYwQg?e=yoroYE&nav=MTVfezFERjEwN0YwLTQ3NTYtRDU0NC1BMjc1LTA0NENBNjU5RDY4Mn0'],
                ],
            ],
            [
                'name' => 'HMS 2017 dan 2018 (Expired)',
                'links' => [
                    ['name' => 'Template Form Logbook Bimbingan TA (Khusus HMS)', 'link' => 'https://telkomuniversityofficial-my.sharepoint.com/:w:/g/personal/bif_telkomuniversity_ac_id/Eb-oQdzpFbJDstO1ITXornMBt8dO8Y6b7hwVtME_CpjKHg?e=9bxK2F'],
                    ['name' => 'Form Upload Logbook Bimbingan Online (Khusus HMS)', 'link' => 'https://forms.office.com/r/BCAVGza7bN'],
                ],
            ],
        ];

        $orderedLinks = $this->orderedLinks($sections);
        $this->createMissingLinks($orderedLinks);
        $this->spreadBatchTimestamps($orderedLinks);
    }

    private function orderedLinks(array $sections): array
    {
        $orderedLinks = [];

        foreach ($sections as $sectionData) {
            $section = ImportantSection::where('name', $sectionData['name'])->first();

            if ($section === null) {
                continue;
            }

            foreach ($sectionData['links'] as $linkData) {
                $orderedLinks[] = [
                    'important_section_id' => $section->id,
                    'name' => $linkData['name'],
                    'link' => $linkData['link'],
                ];
            }
        }

        return $orderedLinks;
    }

    private function staggeredTimestamp(int $position, int $total): Carbon
    {
        return Carbon::create(2026, 9, 7)->startOfDay()->subDays($total - 1 - $position);
    }

    private function createMissingLinks(array $orderedLinks): void
    {
        $total = count($orderedLinks);

        foreach ($orderedLinks as $position => $linkData) {
            $link = ImportantLink::firstOrCreate(
                ['important_section_id' => $linkData['important_section_id'], 'name' => $linkData['name']],
                ['link' => $linkData['link']]
            );

            if ($link->wasRecentlyCreated) {
                $timestamp = $this->staggeredTimestamp($position, $total);
                ImportantLink::whereKey($link->id)->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            }
        }
    }

    private function spreadBatchTimestamps(array $orderedLinks): void
    {
        $total = count($orderedLinks);
        $positionByKey = [];

        foreach ($orderedLinks as $position => $linkData) {
            $positionByKey[$this->linkKey($linkData['important_section_id'], $linkData['name'])] = $position;
        }

        $managedLinks = ImportantLink::all()->filter(
            fn (ImportantLink $link) => isset($positionByKey[$this->linkKey($link->important_section_id, $link->name)])
        );

        $uneditedLinks = $managedLinks->filter(
            fn (ImportantLink $link) => $link->created_at->equalTo($link->updated_at)
        );

        foreach ($uneditedLinks->groupBy(fn (ImportantLink $link) => $link->updated_at->toDateTimeString()) as $batch) {
            if ($batch->count() < 2) {
                continue;
            }

            foreach ($batch as $link) {
                $timestamp = $this->staggeredTimestamp($positionByKey[$this->linkKey($link->important_section_id, $link->name)], $total);
                ImportantLink::whereKey($link->id)->update(['created_at' => $timestamp, 'updated_at' => $timestamp]);
            }
        }
    }

    private function linkKey(int $sectionId, string $name): string
    {
        return $sectionId.'|'.$name;
    }
}