<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            'Timeline Tugas Akhir' => [
                'subtitle' => 'Timeline Tugas Akhir',
                'body' => '<p>Berikut merupakan timeline dari Tugas Akhir</p>',
                'image' => null,
                'created_at' => '2024-01-02',
                'updated_at' => '2024-01-02',
            ],
            'Panduan Tugas Akhir' => [
                'subtitle' => 'Panduan Tugas Akhir',
                'body' => '<p>Berikut panduan untuk Tugas Akhir, dokumennya dapat dilihat pada Menu <strong>Link Penting</strong></p>',
                'image' => null,
                'created_at' => '2024-01-02',
                'updated_at' => '2024-01-03',
            ],
            'Hasil Registrasi Bayangan Genap 2023/2024' => [
                'subtitle' => 'Registrasi Bayangan MK Pilihan dan Penulisan Proposal',
                'body' => '<p>Berikut terlampir hasil registrasi bayangan yang telah diselenggarakan di Prodi S1 Informatika pada tanggal 19 hingga 24 Januari 2024.</p>
<ol>
<li>Mahasiswa diplot berdasarkan urutan submit, kuota kelas MK dan juga KK direncana topik yang akan diambil.</li>
<li>Apabila ada mahasiswa yang merasa sudah submit tetapi namanya tidak muncul, maka ada kemungkinan tidak memilih MK pilihan di KK manapun atau kelas KK dari MK yang dipilih telah penuh. Silahkan isi form feedback.</li>
<li>MK hasil registrasi tidak perlu dipilih saat registrasi nanti. Sistem akan melakukan inject secara otomatis. Mungkin tidak langsung di hari pertama registrasi, jadi mohon untuk bersabar.</li>
<li>Sisakan slot SKS untuk MK Pilihan hasil registrasi bayangan ini.</li>
</ol>
<p><a href="https://info-bif.telkomuniversity.ac.id/links">https://info-bif.telkomuniversity.ac.id/links</a> atau</p>
<p><strong>Hasil Registrasi Bayangan</strong></p>
<p><strong>Form Feedback Hasil Registrasi</strong></p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-01-26',
                'updated_at' => '2024-01-26',
            ],
            'KEBIJAKAN REGISTRASI GENAP TA 23/24' => [
                'subtitle' => 'Informasi ini berisi kebijakan dan arahan Prodi terkait permasalahan registrasi',
                'body' => '<p>Mata kuliah <strong>Informatika untuk Masyarakat</strong> diperbolehkan bentrok. Mata kuliah <strong>Penulisan Proposal</strong> dan <strong>Tugas Akhir</strong> tidak boleh diambil bersamaan.</p>
<p>Mahasiswa yang namanya tidak terdaftar pada Hasil Registrasi Bayangan Proposal SiPProp, silahkan memilih kelas Proposal sesuai KK dari Calon Pembimbing pada saat input MK di Sirama. Mohon untuk <strong>TIDAK MENGAMBIL MK dengan kode berawalan &quot;CPI&quot;</strong></p>
<p>Contoh CP1F4 - Algoritma Pemrograman</p>
<p>tetapi yang diambil harusnya yang berawalan &quot;CII&quot;</p>
<p>Contoh CII1F4 - Algoritma Pemrograman</p>
<h2>MK yang akan diinject otomatis ke Sirama adalah (mohon bersabar menunggu apabila sampai saat ini belum terinject ke Sirama)</h2>
<ol type="a">
<li>MK Pilihan hasil registrasi bayangan</li>
<li>MK Penulisan Proposal hasil registrasi bayangan</li>
<li>MK MBKM yang diajukan konversinya</li>
</ol>
<p>Proses inject MK Pilihan dan Proposal sudah selesai dilakukan hari selasa. Proses inject bisa gagal karena saat sistem melakukan inject, kuota sks mahasiswa sudah penuh. Mohon mahasiswa menambahkan secara manual apabila ternyata belum ter-inject MK tersebut.</p>
<h2>Mohon untuk TIDAK PC/DM ke Prodi terkait permasalahan registrasi.</h2>
<p>Diskusikan dengan dosen wali, apabila solusi tidak ditemukan baru mengajukan ke Prodi via dosen wali. Pertanyaan umum bisa ditanyakan di group telegram &quot;20xy S1 IF with Prodi&quot;</p>
<h2>Perubahan jadwal MK Bahasa Inggris untuk Karir kelas IF-45-GABUP07, dimajukan satu jam menjadi 7:30 hingga 10:30 WIB.</h2>
<p>Penambahan kelas MK Bahasa Inggris untuk Presentasi kelas IF-45-GAB07, dengan jadwal Senin 15:30-17:30 dan Selasa 10:30-12:30 WIB.</p>
<h2>Perubahan Jadwal Bahasa Inggris untuk Presentasi</h2>
<p>IF-45-GAB02<br>SELASA 10:30 - 12:30 KU3.05.13 BAHASA INGGRIS UNTUK PRESENTASI<br>KAMIS 08:30 - 10:30 KU3.03.02 BAHASA INGGRIS UNTUK PRESENTASI</p>
<p>IF-45-GAB03<br>SELASA 10:30 - 12:30 (A308A) KU1.03.11 BAHASA INGGRIS UNTUK PRESENTASI<br>KAMIS 12:30 - 14:30 (A207B) KU1.02.14 BAHASA INGGRIS UNTUK PRESENTASI</p>
<p>IF-46-GABUP03<br>SELASA 08:30 - 10:30 KU3.04.20 BAHASA INGGRIS UNTUK PRESENTASI<br>KAMIS 10:30 - 12:30 KU3.04.19 BAHASA INGGRIS UNTUK PRESENTASI</p>
<p>IF-46-GABUP05<br>KAMIS 10:30 - 12:30 (A310) KU1.03.14 BAHASA INGGRIS UNTUK PRESENTASI<br>SELASA 12:30 - 14:30 (A207A) KU1.02.13 BAHASA INGGRIS UNTUK PRESENTASI</p>',
                'image' => null,
                'created_at' => '2024-01-29',
                'updated_at' => '2024-02-02',
            ],
            'Perubahan Masa Studi (PRS) Genap TA 2023/2024' => [
                'subtitle' => 'Informasi umum PRS',
                'body' => '<p>Berdasarkan informasi awal dari BSLA bahwa PRS dilaksanakan pada tgl 26 Februari s.d. 1 Maret 2024 di iGracias. Di luar masa tersebut maka tidak akan diproses oleh BSLA ataupun Bagian Akademik.</p>
<h2>Adapun beberapa hal yang bisa dilakukan oleh Dosen Wali:</h2>
<ul>
<li>Drop MK</li>
<li>Pindah kelas/MK karena disebabkan bentrok atau MBKM onsite (sesuai sisa kuota kelas).</li>
<li>Input MK untuk mahasiswa yang terlambat registrasi dan HMS 2017.</li>
</ul>
<h2>Beberapa hal yang perlu dipastikan hingga masa PRS berakhir:</h2>
<ul>
<li>Jadwal perkuliahan mahasiswa tidak bentrok.</li>
<li>Berkas pengajuan cuti dan undur diri mahasiswa sudah lengkap diunggah dan diacc via iGracias.</li>
<li>Mahasiswa telah melunasi BPP atau status pengajuan cicilan sudah disetujui 100%.</li>
</ul>
<h2>MK yang diperbolehkan bentrok adalah:</h2>
<ul>
<li>Sosio Informatika dan Keprofesian</li>
<li>Informatika untuk Masyarakat</li>
</ul>
<h2>Daftar MK yang dihapus (Silahkan kontak Dosen Wali atau Prodi terkait Pemindahan Kelas)</h2>
<p><a href="https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/Eb1KSM_treBCuRwT0d6-QOsBIOYthhI00lvxV_7vG8o9wQ?e=KLSI4v">https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/Eb1KSM_treBCuRwT0d6-QOsBIOYthhI00lvxV_7vG8o9wQ?e=KLSI4v</a></p>
<p>Informasi lainnya seperti jadwal perkuliahan bisa diakses di INFO-BIF <a href="https://info-bif.telkomuniversity.ac.id/links">https://info-bif.telkomuniversity.ac.id/links</a></p>
<p>Terima kasih sebelumnya 🙏</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-02-25',
                'updated_at' => '2024-02-25',
            ],
            'Plotting Dosen Pembimbing Akademik MK IuM' => [
                'subtitle' => 'Informatika untuk Masyarakat Prodi S1 IF, Genap 2023/24',
                'body' => '<p>Berikut kami sampaikan informasi terkait Plotting Dosen Pembimbing Akademik MK Informatika untuk Masyarakat.</p>
<p><strong>Plotting Dosen dan Tim</strong> &gt;&gt; LINK</p>
<p><strong>Panduan IuM 2024</strong> &gt;&gt; LINK</p>
<p><strong>Timeline IuM 2024</strong> &gt;&gt; LINK</p>
<p><strong>Rekaman Sosialisasi IuM 2024</strong> &gt;&gt; LINK</p>
<h3>Catatan:</h3>
<ul>
<li>Setiap kelompok wajib mencantumkan link group WA/Telegram beserta ketua kealsnya.</li>
<li>Dosen pembimbing diharapkan join ke group yang telah dibuat.</li>
<li>Mahasiswa diharapkan untuk segera menghubungi Dosen Pembimbing masing-masing.</li>
<li>Dosen yang akan melakukan klaim kegiatan IuM sebagai Abdimas, maka proses administrasi di SiPeMa dilakukan secara mandiri oleh masing-masing dosen (bisa dibantu oleh tim mahasiswa).</li>
<li>Dipersilahkan menghubungi Prodi apabila ada informasi yang tidak sesuai atau ingin ditanyakan.</li>
</ul>
<p>Terima kasih</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-03-07',
                'updated_at' => '2024-05-10',
            ],
            'Sidang Tugas Akhir S1 IF' => [
                'subtitle' => 'Jenis-jenis sidang Tugas Akhir S1 IF',
                'body' => '<p>Pendaftaran Sidang TA dapat dilakukan kapan saja, tanpa harus menunggu akhir semester.</p>
<p>Berikut adalah jenis-jenis sidang TA yang ada di Prodi S1 Informatika, Fakultas Informatika, Unversitas Telkom.</p>
<h2>Sidang Non Terjadwal</h2>
<p>Mahasiswa harus mempersiapkan berkas sidang secara lengkap di awal untuk melakukan pendaftaran sidang dan harus sudah memperoleh izin minimal dari dosen pembimbing 1 (satu).</p>
<h2>Sidang Regular</h2>
<p>Mahasiswa melakukan pendaftaran sidang tugas akhir melalui link <strong>Form Pendaftaran Sidang</strong> dengan menyertakan seluruh berkas lengkap syarat sidang tugas akhir pada form pendaftaran.</p>
<h2>Seminar Internal Pengganti Sidang</h2>
<p>Mahasiswa dapat mengganti Sidang TA dengan Seminar Internal apabila telah melakukan publikasi Tugas Akhir berupa Jurnal/Conference/HAKI.</p>
<p>Syarat utama: telah mendapatkan LoA (Letter of Acceptance) dan menyelesaikan revisi jurnal/conference paper/HKI</p>
<h3>Catatan:</h3>
<p>Untuk mahasiswa yang sudah melakukan presentasi pada conference yang diterima tidak perlu melakukan seminar internal. cukup melampirkan sertifikat sebagai presenter pada form pendaftaran Yudisium.</p>
<h2>Sidang Terjadwal</h2>
<p>Merupakan sidang khusus yang mana Berkas Kelengkapan Sidang TA dapat disusulkan setelah Sidang TA selesai dilakukan.</p>
<h2>Sidang Terjadwal</h2>
<p>Semua mahasiswa yang merasa sudah siap sidang TA, namun belum selesai menyiapkan berkas syarat sidang, diperkenankan mendaftar di sesi terjadwal.</p>
<h2>Sidang Terjadwal Khusus</h2>
<p>Prodi akan mendaftarkan mahasiswa yang berpotensi Lulus Tepat Waktu (LTW) 4 tahun dan mahasiswa Habis Masa Studi (HMS).</p>
<p>Mahasiswa yang telah melakukan Sidang TA untuk bisa segera menyelesaikan revisi dan mendaftar Sidang Yudisium untuk bisa lulus menjadi Sarjana.</p>
<h2>Perbedaan Sidang Yudisium vs Sidang TA</h2>
<p>Sidang Yudisium adalah sidang tertutup yang hanya dihadiri oleh Wakil Dekan Bagian Akademik, Prodi, Dosen Wali dan LAA yang akan menyatakan kelulusan mahasiswa menjadi Sarjana. Mahasiswa dikatakan lulus apabila:</p>
<ol>
<li>SKS lulus minimal 144 SKS (semua MK Wajib telah diambil dan lulus)</li>
<li>IPK tidak kurang dari 2.</li>
<li>Lunas secara administrasi (BPP dan Uang Kelulusan)</li>
<li>TAK telah sesuai ketentuan untuk bisa lulus.</li>
<li>Telah melakukan Sidang TA/Pengganti Sidang.</li>
<li>Telah unggha berkas sidang TA ke OpenLib.</li>
<li>Bebas pinjaman buku dari Perpustakaan.</li>
<li>Menyumbang buku ke Perpustakaan.</li>
<li>Tidak sedang menjalani sanksi akademik</li>
</ol>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-03-22',
                'updated_at' => '2024-03-22',
            ],
            'Predikat Kelulusan Sarjana' => [
                'subtitle' => 'Jenis-jenis predikat dan syaratnya',
                'body' => '<p>Syarat cumlaude FIF per Mei 2018:</p>
<p>Di Informasikan kepada Mahasiswa Fakultas Informatika bahwa yang berhak dinyatakan lulus dengan mendapatkan predikat <strong>CUMLAUDE</strong> harus memenuhi persyaratan berikut:</p>
<ul>
<li>Memenuhi syarat sesuai pasal 34 (Predikat kelulusan), 40 (Kewajiban Publikasi Karya Akhir untuk Persyaratan Kelulusan Studi), dan 41 (Kewajiban Lulus Ujian Kecakapan Bahasa Asing) pada Pedoman Akademik Universitas Telkom Tahun 2022. LINK</li>
<li>Memenuhi persyaratan berikut ini dan menyerahkan bukti tertulis penerimaan artikel publikasi ilmiah (Letter of Acceptance/LoA) paling lambat pada saat pendaftaran sidang yudisium.</li>
</ul>
<h2>Mahasiswa Reguler</h2>
<ul>
<li>Menjalani Studi Maksimal 8 Semester</li>
<li>IPK ≥ 3,51</li>
<li>Menyerahkan LoA Publikasi</li>
</ul>
<h2>Mahasiswa Ekstensi</h2>
<h2>D3 Lulusan dengan predikat Cumlaude</h2>
<ul>
<li>Menjalani Studi Maksimal 4 Semester</li>
<li>IPK ≥ 3,51</li>
<li>Menyerahkan LoA Publikasi</li>
</ul>
<p>Berdasarkan hasil Rapat Pimpinan Fakultas Informatika pada tanggal 11 April 2018 bahwa Nama mahasiswa harus sebagai penulis pertama pada publikasi ilmiah yang diajukan sebagai syarat Cumlaude. Ketentuan ini berlaku mulai Yudisium periode Mei 2018.</p>
<p>Lulusan yang memenuhi persyaratan IPK untuk mendapatkan predikat kelulusan Dengan Pujian (Excellent/Cumlaude) tetapi tidak dapat memenuhi persyaratan tambahan sesuai waktu yang ditentukan Fakultas, maka predikat kelulusan yang diberikan adalah Sangat Memuaskan (Very Good).</p>
<p>Demikian disampaikan atas perhatiannya diucapkan terima kasih.</p>
<p>referensi: <a href="https://soc.telkomuniversity.ac.id/persyaratan-mahasiswa-dinyatakan-cumlaude-di-fakultas-informatika/">https://soc.telkomuniversity.ac.id/persyaratan-mahasiswa-dinyatakan-cumlaude-di-fakultas-informatika/</a></p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-03-22',
                'updated_at' => '2024-05-14',
            ],
            'PENGUMPULAN LAPORAN AKHIR IUM' => [
                'subtitle' => 'Informatika untuk Masyarakat Prodi S1 IF, Genap 2023/24',
                'body' => '<p>Teman-teman S1 IF yang mengambil MK IUM, Laporan Akhir sudah bisa dikumpulkan di LMS.</p>
<p>Link RPS di LMS sudah diupdate.</p>
<p>Template dan kelengkapan berkas menyesuaiakan Panduan IuM S1 IF 2024</p>
<p><strong>Deadline Laporan Akhir</strong> Sabtu, 15 Juni 2024, 23:45 WIB (diperpanjang menjadi Jumat 25 Juni 2024, 23:45 WIB. Tidak ada perpanjangan waktu lagi).</p>
<h3>Catatan Penting:</h3>
<ol>
<li>Silahkan melakukan Presentasi terlebih dahulu dan menyelesaikan revisi dari dosen. Bentuk presentasi bisa langsung/VCon Synchronous/Rekaman Video. Mohon ditanyakan bentuk presentasi ke dosen pembimbing.</li>
<li>Laporan Akhir yang dikumpulkan adalah versi final hasil revisi dengan dosen pembimbing akademik (ditanda tangan oleh Tim, Dosen Pembimbing dan Mitra).</li>
<li>Nilai IuM akan diproses apabila telah mengumpulkan laporan akhir dan dosen pembimbing telah submit nilai di form yang disediakan oleh Prodi.</li>
<li>Untuk yang melakukan konversi dari Abdimas dosen/ Innovillage/ Kegiatan lain yang sudah disetujui oleh Prodi, mohon tetap mengumpulkan Laporan Akhir IuM dengan melakukan reformating dari dokumen sebelumnya. Sesuikan dengan Panduan IuM.</li>
<li>Status Rekap Nilai oleh Dosen bisa dilihat di excel Plotting Kelompok IuM.</li>
</ol>
<p>Terima kasih</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-06-02',
                'updated_at' => '2024-06-23',
            ],
            'REGISTRASI BAYANGAN MK PROPOSAL S1 IF GANJIL TA 2024/2025' => [
                'subtitle' => 'REGISTRASI BAYANGAN MK PROPOSAL S1 IF',
                'body' => '<p>[ REGISTRASI BAYANGAN MK PROPOSAL S1 IF]</p>
<p>Kepada seluruh mahasiswa S1 Informatika yang akan mengambil MK Penulisan Proposal.</p>
<p>Dalam rangka persiapan registrasi Ganjil 24/25 Prodi mengadakan Registrasi Bayangan MK Penulisan Proposal.</p>
<p>Registrasi tidak bersifat wajib.</p>
<p>Prodi hanya menfasilitasi mahasiswa yang sudah memiliki bayangan topik TA untuk mencari pembimbing pada saat liburan semester ini.</p>
<h2>Registrasi bayangan dilakukan di aplikasi SiPProp.</h2>
<h2>Melalui aplikasi SiPProp, mahasiswa bisa melakukan:</h2>
<ul>
<li>Pencarian dosen calon pembimbing beserta informasi topik penelitian dosen.</li>
<li>Pengajuan dosen sebagai calon pembimbing.</li>
</ul>
<p>Registrasi bayangan dikatakan selesai apabila mahasiswa sudah memperoleh persetujuan dari calon pembimbing.</p>
<p>Mahasiswa yang tidak mengikuti registrasi bayangan tetap bisa mengajukan calon pembimbing pada perkuliahan MK Penulisan Proposal di Semester Ganjil TA 24/25.</p>
<h2>Hal yang perlu diperhatikan dosen memiliki kuota mahasiswa bimbingan.</h2>
<p><strong>Batas registrasi bayangan:</strong> 16 Agustus 2024 pukul 23.59 WIB</p>
<p><strong>Aplikasi SiPProp:</strong> <a href="https://apps-soc.telkomuniversity.ac.id">https://apps-soc.telkomuniversity.ac.id</a> (Login SSO 365)</p>
<p>Sekian dan terima Kasih</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-08-09',
                'updated_at' => '2024-08-09',
            ],
            'HASIL REGISTRASI BAYANGAN MK PROPOSAL S1 IF GANJIL TA 2024/2025' => [
                'subtitle' => 'REGISTRASI BAYANGAN MK PROPOSAL S1 IF',
                'body' => '<p>Kepada seluruh mahasiswa S1 Informatika yang akan mengambil MK Penulisan Proposal, berikut kami lampirkan hasil registrasi bayangan MK Penulisan Proposal Ganjil 2024/25:</p>
<p><a href="https://tel-u.ac.id/zx7ctd3tasx8ozgz2bqczaxrpj0wfv">https://tel-u.ac.id/zx7ctd3tasx8ozgz2bqczaxrpj0wfv</a></p>
<h3>Catatan Penting:</h3>
<ol>
<li>Mahasiswa yang namanya tercantum pada tabel hasil registrasi, tidak perlu memilih MK Proposal di aplikasi SIRAMA ataupun iGracias. Prodi sudah mendaftarkan mahasiswa tersebut untuk dilakukan Preload/Inject MK Proposal pada kelas Mata Kuliah.</li>
<li>Mahasiswa tidak bisa drop MK hasil preload ini. Apabila mendesak, maka harus izin langsung kepada Prodi S1 Informatika.</li>
<li>Mahasiswa yang tidak mengikuti registrasi bayangan (namanya tidak tercantum pada tabel di link) tetap bisa memilih kelas MK Penulisan Proposal pada saat registrasi semester.</li>
<li>Pemilihan kelas MK Penulisan Proposal bebas sesuai dengan rencana studi mahasiswa dan sisa kuota kelas.</li>
<li>Pemilihan topik dan calon pembimbing dilakukan pada saat perkuliahan ganjil berjalan. Mohon mengikuti instruksi dari dosen kelas MK Penulisan Proposal.</li>
</ol>
<p>Demikian informasi ini kami sampaikan, terima kasih.</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-08-27',
                'updated_at' => '2024-08-27',
            ],
            'Informasi PRS Ganjil 2024/25' => [
                'subtitle' => 'Perubahan Rencana Studi, Pengajuan Cuti, Undur Diri dan Registrasi HMS',
                'body' => '<p>Informasi pada halaman ini akan diupdate secara berkala selama periode PRS. Notifikasi perubahan akan disampaikan via group telegram angkatan bersama Prodi.</p>
<h2>Ketentuan Umum Perubahan Rencana Studi (PRS):</h2>
<ul>
<li>Masa PRS adalah 23 hingga 27 September 2024 via iGracias.</li>
<li>Mahasisa menyelesaikan permasalahan registrasi Ganjil TA 2024/25 di masa PRS ini. Apabila terlewat maka tidak akan ada perubahan yang bisa dilakukan lagi. Mohon dipastikan jadwal perkuliahan sudah tidak bentrok.</li>
<li>Akses PRS hanya diberikan kepada Dosen Wali dan Prodi.</li>
<li>Mahasiswa tidak dapat menambah mata kuliah untuk melengkapi sisa kuota SKS.</li>
<li>Mahasiswa diperbolehkan untuk drop mata kuliah yang dirasa tidak sesuai, kecuali MK konversi MBKM. Prodi akan melakukan pengecekan dan nilai MK semester berjalan akan di-hold untuk mahasiswa yang drop MK konversi MBKM.</li>
<li>Mahasiswa diperbolehkan pindah/mengganti kelas MK apabila bentrok proses ini dilakukan via Prodi.</li>
<li>Untuk mahasiswa yang telah diizinkan mengambil kelas online karena mengikuti magang onsite, proses perpindahan kelas dilakukan oleh prodi. Silahkan cek informasi yang ada di grup secara berkala.</li>
<li>Registrasi khusus mahasiswa HMS angkatan 2018 dilakukan pada masa PRS ini.</li>
<li>Mahasiswa yang akan menghubungi prodi secara langsung tidak akan dilayani, silahkan melalui link permasalahan registrasi yang disediakan prodi via dosen wali.</li>
</ul>
<h2>Kebijakan Khusus Prodi S1 Informatika:</h2>
<ul>
<li>Mahasiswa angkatan 2023 dan 2022 yang belum lulus/mengambil MK Bahasa Inggris, maka Wajib mengambil di Ganjil ini, karena MK tersebut adalah MK Tingkat 1 yang harus lulus segera untuk tidak DO. Terdapat 3 kelas yang dibuka dengan banyak sisa kuota.</li>
</ul>
<h2>Terdapat pergantian jadwal perkuliahan, yaitu:</h2>
<ul>
<li>Kalkulus IFX-48-GAB</li>
<li>Sistem Operasi IFX-48-GAB</li>
<li>Analisis dan Perancangan Perangkat Lunak IF-47-GAB05</li>
<li>Implementasi dan Pengujian Perangkat Lunak IF-46-05</li>
<li>Implementasi dan Pengujian Perangkat Lunak IF-46-12</li>
<li>Penulisan Proposal IF-PROP-HUMIC</li>
</ul>
<h2>Terdapat pergantian ruang perkuliahan, yaitu:</h2>
<ul>
<li>Analisis Kompleksitas Algoritma IF-47-11</li>
<li>Analisis Kompleksitas Algoritma IF-47-12</li>
</ul>
<h2>Terdapat penutupan kelas mata kuliah, yaitu:</h2>
<ul>
<li>Matriks dan Ruang Vektor IF-47-GAB02</li>
</ul>
<p>mahasiswa harap melakukan pindah kelas via Dosen Wali/Prodi</p>
<h2>Terdapat penambahan kelas mata kuliah, yaitu:</h2>
<ul>
<li>Manajemen Proyek TIK IF-46-GAB08</li>
<li>Analisis Jejaring Sosial IF-45-DSIS.03</li>
<li>Sosio-Informatik dan Keprofesian IF-45-GAB</li>
</ul>
<p>Update jadwal perkuliahan bisa dilihat pada <a href="https://tel-u.ac.id/fotigsweyqkn1e9nmidl6fs6n0v17f">https://tel-u.ac.id/fotigsweyqkn1e9nmidl6fs6n0v17f</a></p>
<p>Mahasiswa yang masuk kelas Prodi S1-PJJ Informatika tanpa seizin Prodi S1 Informatika akan dipindahkan ke kelas reguler.</p>
<h2>Mahasiswa yang mengambil WRAP Research CoE CAATIS harus mengambil mata kuliah berikut:</h2>
<ul>
<li>WRAP Researchship - Pengembangan dan Implementasi Riset (UFKXCEB) dengan bobot 4 SKS</li>
<li>WRAP Researchship - Perancangan dan Pengujian Riset (UFKXDEB) dengan bobot 4 SKS</li>
<li>Penulisan Proposal IF-PROP-CITI</li>
</ul>',
                'image' => 'images/placeholder.png',
                'created_at' => '2024-09-19',
                'updated_at' => '2024-09-24',
            ],
            'REGISTRASI BAYANGAN MK PILIHAN PRODI S1 INFORMATIKA' => [
                'subtitle' => 'Semester Genap TA 2024/2025',
                'body' => '<p>Teman-teman.. berikut kami lampirkan terkait link form registrasi dan rekaman sosialisasi registrasi bayangan MK Pilihan Prodi S1 Informatika</p>
<p><strong>Link Rekaman:</strong> <a href="https://youtu.be/T16ujVtv5JA">https://youtu.be/T16ujVtv5JA</a></p>
<p><strong>Slide Materi Sosialisasi:</strong> <a href="https://tel-u.ac.id/y5wd48oy0sa3sdeyt7jetbavi0457s">https://tel-u.ac.id/y5wd48oy0sa3sdeyt7jetbavi0457s</a></p>
<p><strong>Link Form Registrasi Bayangan:</strong> <a href="https://forms.office.com/r/K7h2AcYNit">https://forms.office.com/r/K7h2AcYNit</a> (1 akun hanya bisa submit 1x)</p>
<p><a href="https://forms.office.com/r/K7h2AcYNit">https://forms.office.com/r/K7h2AcYNit</a> (1 akun hanya bisa submit 1x)</p>
<p><a href="https://forms.office.com/r/K7h2AcYNit">https://forms.office.com/r/K7h2AcYNit</a> (1 akun hanya bisa submit 1x)</p>
<p><a href="https://forms.office.com/r/K7h2AcYNit">https://forms.office.com/r/K7h2AcYNit</a> (1 akun hanya bisa submit 1x)</p>
<p><strong>Rencana Studi</strong> <a href="https://tel-u.ac.id/2r3jrru37feh9s14-k6lgprzq000nv">https://tel-u.ac.id/2r3jrru37feh9s14-k6lgprzq000nv</a></p>
<p><strong>Katalog Mata Kuliah Pilihan</strong> <a href="https://tel-u.ac.id/k5xr0meuhoqvaccbbjpt3dux37vm8o">https://tel-u.ac.id/k5xr0meuhoqvaccbbjpt3dux37vm8o</a></p>
<p><strong>Buku Saku Kurikulum 2024</strong> <a href="https://tel-u.ac.id/ghksdggnbee2cx6qy9y-f8la91ssa4">https://tel-u.ac.id/ghksdggnbee2cx6qy9y-f8la91ssa4</a></p>
<p>Terima kasih</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-01-23',
                'updated_at' => '2025-01-23',
            ],
            'HASIL REGISTRASI BAYANGAN MK PILIHAN S1 INFORMATIKA' => [
                'subtitle' => 'SEMESTER GENAP 2024/25',
                'body' => '<p>Berikut kami lampirkan Hasil Plotting Registrasi Bayangan MK Pilihan yang telah dilakukan:</p>
<p><a href="https://tel-u.ac.id/efuyqii2hjt7quhwr901lwbhjcr8po">https://tel-u.ac.id/efuyqii2hjt7quhwr901lwbhjcr8po</a></p>
<h3>Catatan Penting:</h3>
<ol>
<li>Mahasiswa yang namanya terdaftar pada hasil registrasi bayangan, maka tidak perlu memilih MK tersebut secara manual pada saat registrasi. Prodi akan melakukan inject pada saat registrasi di SIRAMA, sehingga mohon sisakan kuota SKS, sehingga MK Pilihan dapat di-inject nanti.</li>
<li>Mahasiswa dapat melakukan drop MK Pilihan hasil registrasi di SIRAMA via Dosen Wali/Prodi. Tetapi Prodi tidak akan membantu mengembalikan ke MK Pilihan yang di-drop, apabila kuota MK Pilihan yang dituju penuh.</li>
<li>Mahasiswa yang tidak memperoleh MK Pilihan karena kehabisan kuota, maka dipersilahkan memilih MK Pilihan yang tersedia pada saat registrasi semester.</li>
<li>Mahasiswa dapat memberikan feedback &amp; komplain pada form berikut ini: <a href="https://forms.office.com/r/ThQLXP3WsD">https://forms.office.com/r/ThQLXP3WsD</a> Link tanggapan dari Prodi sudah dicantumkan pada form tersebut.</li>
<li>Pertanyaan di group ataupun pribadi tidak akan diresponse, silahkan melalui form yang telah disediakan tersebut.</li>
</ol>
<p>Terima kasih</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-01-29',
                'updated_at' => '2025-01-29',
            ],
            'Topic Shopping Semester Genap TA 2024/25' => [
                'subtitle' => 'Topic Shopping Penelitian Dosen untuk MK Penulisan Proposal',
                'body' => '<p>Berikut kami lampirkan informasi terkait Topic Shopping Penelitian Dosen untuk MK Penulisan Proposal dan Tugas Akhir.</p>
<p>Mahasiswa yang berencana mengambil MK Penulisan Proposal di Genap untuk <strong>WAJIB MEMBACA DAN MENONTON REKAMAN</strong> berikut ini:</p>
<p><strong>Slide:</strong> <a href="https://tel-u.ac.id/awfpeur95orgjxw-qrl9j2j5l-ob64">https://tel-u.ac.id/awfpeur95orgjxw-qrl9j2j5l-ob64</a></p>
<p><strong>Rekaman Video Penjelasan:</strong> <a href="https://youtu.be/TvpfIdGF0RQ?feature=shared">https://youtu.be/TvpfIdGF0RQ?feature=shared</a></p>
<p>Topik penelitian dosen dapat diakses melalui SiProp, silahkan login SSO pada <a href="https://apps-soc.telkomuniversity.ac.id/">https://apps-soc.telkomuniversity.ac.id/</a> kemudian pilih SiProp. Prodi membatasi akses dengan mendaftarkan mahasiswa yang menurut Prodi eligible*.</p>
<h2>Akses SiProp saat ini dibatasi hanya untuk melihat topik penelitian dosen yang ditawarkan untuk tugas akhir.</h2>
<p>Mahasiswa yang belum memperoleh akses ke SiProp, maka tetap bisa mengambil MK Penulisan Proposal pada registrasi semester genap.</p>
<h2>Akses final ke SiProp untuk pengajuan calon dosen pembimbing diberikan untuk semua mahasiswa yang terdaftar di kelas penulisan Proposal Semester Genap TA 2024/25 pada saat registrasi berakhir dan semester genap dimulai.</h2>
<p>Terima kasih</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-02-01',
                'updated_at' => '2025-02-01',
            ],
            'PENGUMPULAN LAPORAN AKHIR IUM PRODI S-1 IF' => [
                'subtitle' => 'Informatika untuk Masyarakat Genap TA 2024/25',
                'body' => '<p>(Informasi Khusus untuk Mahasiswa)</p>
<p>Teman-teman mahasiswa, berikut kami sampaikan terkait teknis umum pengumpulan Laporan Akhir IUM.</p>
<h2>Luaran WAJIB IUM:</h2>
<ul>
<li>Video Kegiatan yang diunggah ke Youtube.</li>
<li>Publikasi artikel di media massa:
<ul>
<li>WAJIB untuk kelompok yang dosen pembimbing yang akan klaim sebagai ABDIMAS (mengikuti ketentuan ABDIMAS dari PPM Tel-U, minimal website Fakultas ataupun KK). Catatan: Mahasiswa dipersilahkan menghubungi dosen pembimbing apabila dosen yang bersangkutan terkait teknis publikasi artikel di website KK ataupun Fakultas.</li>
<li>Berkas yang dipersiapkan link drive yang berisi 3 file foto &amp; dokumen yang deskripsi kegiatan IUM (judul, tim pelaksana &amp; dosen, dan abstract/summary kegiatan)</li>
<li>Cukup sosial media (instagram, twitter, facebook, dll) untuk kelompok IUM yang tidak akan diklaim sebagai ABDIMAS.</li>
</ul>
</li>
</ul>
<h2>Penilaian IUM:</h2>
<ul>
<li>Presentasi (teknisnya bisa bertanya ke dosen pembimbing akademik)</li>
<li>Dosen akan mengisi dokumen penilaian IUM dapat diunduh dari <a href="https://tel-u.ac.id/5h50h96p8grh7jpni-lpeurox-winp">https://tel-u.ac.id/5h50h96p8grh7jpni-lpeurox-winp</a></li>
<li>Dosen akan submit form penilaian di link khusus yang sudah disediakan oleh Prodi.</li>
<li>Deadline unggah nilai IUM oleh dosen maksimal adalah Jumat, 20 Juni 2025, pukul 23.45 WIB</li>
</ul>
<h2>Berkas laporan akhir akan diunggah ke LMS (activity akan dibuka pada tanggal 5 Juni 2025). Ada beberapa berkas yang harus dipersiapkan:</h2>
<ul>
<li>Surat Kesediaan Mitra IUM (submit ulang yang diunggah sebelumnya).</li>
<li>Proposal IUM yang sudah diberi tanda-tangan oleh Dosen Pembimbing dan Mitra (submit ulang yang diunggah sebelumnya).</li>
<li>Berita Acara Pelaksanaan IUM.</li>
<li>Laporan Akhir IUM yang sudah diberi tanda tangan oleh Dosen Pembimbing.</li>
</ul>
<p>Demikian informasi ini kami sampaikan, atas perhatiannya terima kasih.</p>
<p>Prodi S-1 Informatika</p>
<p>sumber informasi</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-06-04',
                'updated_at' => '2025-06-05',
            ],
            'Update Informasi Registrasi S-1 IF Ganjil TA 2025/26' => [
                'subtitle' => 'Registrasi Semester Ganjil TA 2025/26',
                'body' => '<p>Prodi akan melakukan update berkala terkait tambahan informasi atau update dari registrasi semester Ganjil TA 2025/26. Silahkan lakukan update berkala pada halaman ini</p>
<h2>Daftar mata kuliah yang ditawarkan</h2>
<p>Mahasiswa <strong>HANYA DIPERBOLEHKAN</strong> mengambil mata kuliah dengan prefix kelas IF (khusus MHS Reguler dan Internasional) atau IFX (khusus MHS Ekstensi). Apabila ada SE-05, AK-FINON mohon <strong>TIDAK DIAMBIL</strong> karena mata kuliah tersebut adalah MK nyasar dari fakultas lain.</p>
<p>Pada Excel jadwal perkuliahan sudah ditambahkan sheet &quot;Student sets GAB&quot; untuk melihat komposisi kelas Gabungan. Hal ini untuk membantu pemilihan jadwal pada kelas Gabungan.</p>
<p>Jadwal MK Kelas untuk Struktur Data dan Sistem Operasi sudah ditambahkan ke SIRAMA dan Excel Jadwal Perkuliahan</p>
<p>Mahasiswa angkatan 2024 <strong>TIDAK DIPERKENANKAN</strong> mengambil MK Sosio Infomatika dan Keprofesian. Mata kuliah tersebut sudah melebihi kapasitas, dan khusus dibuka untuk dua angkatan 2023 dan 2022.</p>
<p>Mahasiswa <strong>TIDAK DIPERKENANKAN</strong> mengambil MK Magang Berdampak/MBKM (cirinya kuotanya adalah 1). MK tersebut akan ditambahkan oleh prodi sesuai pengajuan konversi di Ganjil 2025/26. Prodi akan menghapus/drop MK untuk mahasiswa yang ilegal mengambil MK tersebut.</p>
<h2>Daftar mata kuliah yang diperbolehkan bentrok</h2>
<ul>
<li>Sosio Informatika dan Keprofesian (Online)</li>
<li>Computing Project (Onsite 1x di akhir pada saat ekshibisi)</li>
</ul>
<h2>Kelas Internasional</h2>
<p>Daftar MK Pilihan kelas reguler, TA dan Kerja Praktik yang bisa diambil mahasiswa internasional masih sudah bisa dilihat.</p>
<p>Mahasiswa yang memerlukan mengulang MK di kelas reguler, mohon melapor pada Dosen Wali</p>
<h2>UPDATE PRS</h2>
<p>Berikut kami lampirkan daftar mahasiswa yang mengalami pemindahan kelas mata kuliah karena kelas yang dipilih ditutup.</p>
<p><a href="https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/EfGhBSA1xyNAlCpOUZ2TMtoBroEPlbQ734DPepzrkgW25A?e=QaJMcD">https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/EfGhBSA1xyNAlCpOUZ2TMtoBroEPlbQ734DPepzrkgW25A?e=QaJMcD</a></p>
<p>Salam,</p>
<p>Prodi S-1 Informatika</p>',
                'image' => null,
                'created_at' => '2025-09-01',
                'updated_at' => '2025-09-26',
            ],
            'Informasi Pemindahan Ruang Kelas Kuliah Prodi S-1 IF (Sementara)' => [
                'subtitle' => 'Perkuliahan Semester Ganjil TA 2025-26',
                'body' => '<p>Dengan hormat,</p>
<p>Sehubungan dengan adanya informasi dari Direktur Pasca Sarjana dan Advanced Learning terkait renovasi ruang kelas pada beberapa ruang kuliah program Regular dan Internasional di Gedung Tokong Nanas, Telkom University, yang dilaksanakan tanggal 12 November hingga tanggal 18 November 2025 dan dapat diperpanjang hingga 22 November 2025.</p>
<p>Maka berikut kami lampirkan relokasi ruangan kelas:</p>
<table>
<thead>
<tr><th>Jenis</th><th>Hari</th><th>Shift</th><th>Kode MK</th><th>Nama MK</th><th>Kelas</th><th>Dosen</th><th>Ruangan Sebelumnya</th><th>Ruangan Baru</th></tr>
</thead>
<tbody>
<tr><td>Reguler</td><td>KAMIS</td><td>13:30 - 16:30</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GABUP.01</td><td>ATW</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>International</td><td>SENIN</td><td>10:30 - 12:30</td><td>UAKXACB2</td><td>AGAMA ISLAM</td><td>IF-49-INT</td><td>BZN</td><td>KU3.08.12</td><td>KU3.09.17</td></tr>
<tr><td>Reguler</td><td>SELASA</td><td>06:30 - 09:30</td><td>CAK2CAB3</td><td>SISTEM BASIS DATA</td><td>IF-48-03</td><td>DAM</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>International</td><td>KAMIS</td><td>13:30 - 16:30</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-INT</td><td>DGS</td><td>KU3.08.16</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>SELASA</td><td>08:30 - 11:30</td><td>CAK2EAB4</td><td>STRUKTUR DATA</td><td>IF-48-INT</td><td>DQU</td><td>KU3.08.16</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>RABU</td><td>13:30 - 16:30</td><td>CAK4HBB3</td><td>BIG DATA DAN AI</td><td>IF-46-INT</td><td>DRI</td><td>KU3.08.17</td><td>KU3.09.19</td></tr>
<tr><td>Reguler</td><td>JUMAT</td><td>13:30 - 16:30</td><td>CAK2EAB4</td><td>STRUKTUR DATA</td><td>IF-48-07</td><td>FAQ</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>International</td><td>SENIN</td><td>10:30 - 13:30</td><td>CAK2CAB3</td><td>SISTEM BASIS DATA</td><td>IF-48-INT</td><td>GAW</td><td>KU3.08.16</td><td>KU3.09.16</td></tr>
<tr><td>International</td><td>JUMAT</td><td>08:30 - 11:30</td><td>CAK1EAB3</td><td>MATEMATIKA DISKRIT</td><td>IF-49-INT</td><td>IYK</td><td>KU3.08.16</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>RABU</td><td>10:30 - 12:30</td><td>CAK1BAB3</td><td>ALGORITMA DAN PEMROGRAMAN 1</td><td>IF-49-INT</td><td>JMT</td><td>KU3.08.16</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>RABU</td><td>10:30 - 12:30</td><td>UBKXCCB2</td><td>BAHASA INDONESIA</td><td>IF-48-INT</td><td>JNI</td><td>KU3.08.12</td><td>KU3.09.19</td></tr>
<tr><td>Reguler</td><td>SABTU</td><td>08:30 - 11:30</td><td>CAK3EAB3</td><td>KOMPUTASI AWAN DAN TERDISTRIBUSI</td><td>IF-47-GABUP.04</td><td>KIF</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>Reguler</td><td>RABU</td><td>08:30 - 10:30</td><td>CAK1BAB3</td><td>ALGORITMA DAN PEMROGRAMAN 1</td><td>IF-49-06</td><td>LDS</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>International</td><td>KAMIS</td><td>10:30 - 12:30</td><td>UBKXBCB2</td><td>PANCASILA</td><td>IF-49-INT</td><td>OHA</td><td>KU3.08.12</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>RABU</td><td>08:30 - 10:30</td><td>CAK1HDB2</td><td>STATISTIKA</td><td>IF-49-INT</td><td>PHN</td><td>KU3.08.16</td><td>KU3.09.17</td></tr>
<tr><td>International</td><td>SELASA</td><td>09:30 - 11:30</td><td>CAK1CAB3</td><td>KALKULUS</td><td>IF-49-INT</td><td>PHN</td><td>KU3.08.17</td><td>KU3.08.13</td></tr>
<tr><td>International</td><td>JUMAT</td><td>13:30 - 15:30</td><td>CAK1CAB3</td><td>KALKULUS</td><td>IF-49-INT</td><td>PHN</td><td>KU3.08.18</td><td>KU3.09.20</td></tr>
<tr><td>International</td><td>KAMIS</td><td>13:30 - 15:30</td><td>CAK2DAB3</td><td>SISTEM OPERASI</td><td>IF-48-INT</td><td>QOR</td><td>KU3.08.17</td><td>KU3.09.19</td></tr>
<tr><td>International</td><td>SELASA</td><td>14:30 - 16:30</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-46-INT</td><td>RUZ</td><td>KU3.08.12</td><td>KU3.09.19</td></tr>
<tr><td>Reguler</td><td>SENIN</td><td>08:30 - 11:30</td><td>CAK2CAB3</td><td>SISTEM BASIS DATA</td><td>IF-48-12</td><td>UII</td><td>KU3.03.04</td><td>KU3.08.12</td></tr>
<tr><td>International</td><td>SELASA</td><td>15:30 - 18:30</td><td>CAK3CAB3</td><td>KEAMANAN SIBER</td><td>IF-47-INT</td><td>YDN</td><td>KU3.08.17</td><td>KU3.08.13</td></tr>
</tbody>
</table>
<p>Demikian kami tuliskan informasi ini, atas perhatian dan kerja samanya terima kasih.</p>
<p>Prodi S-1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-11-11',
                'updated_at' => '2025-11-11',
            ],
            'Registrasi Bayangan MK Pilihan S-1 IF' => [
                'subtitle' => 'Semester Genap TA 2526',
                'body' => '<p>Teman-teman semua, berikut kami sampaikan link Registrasi Bayangan untuk MK Pilihan yang akan dibuka di semester Genap TA 2025/26 nanti. Adapun beberapa poin yang perlu menjadi catatan adalah sebagai berikut:</p>
<p><strong>Deadline pengisian adalah 3 Desember 2025, pukul 23:59 WIB</strong></p>
<p>Berikut ini link form khusus untuk mahasiswa S-1 Informatika dari angkatan tingkat 3 ke atas.</p>
<h2>Angkatan yang mengikuti Registrasi Bayangan:</h2>
<ul>
<li>Reguler dan Internasional 2023, 2022, 2021, dan 2019</li>
<li>Ekstensi 2025, Ekstensi 2024 dan Ekstensi Pindahan PJJ</li>
</ul>
<p>Registrasi bayangan tidak bersifat wajib, tetapi sebaiknya ikut serta mengisi form.</p>
<p>Registrasi bayangan ini bertujuan untuk menentukan jumlah kelas yang dibuka pada saat registrasi semester.</p>
<p>Mengingatnya rumitnya penjadwalan, maka registrasi bayangan hanya untuk menentukan kelas MK Pilihan yang dibuka atau ditutup. Mahasiswa tetap melakukan pemilihan kelas MK secara mandiri pada saat registrasi. Prodi tidak melakukan preload otomatis MK Pilihan hasil registrasi bayangan ini ke dalam KRS Mahasiswa.</p>
<p><strong>Link Registrasi Bayangan:</strong> <a href="https://forms.cloud.microsoft/r/vPKFDLfsUV">https://forms.cloud.microsoft/r/vPKFDLfsUV</a></p>
<p><a href="https://forms.cloud.microsoft/r/vPKFDLfsUV">https://forms.cloud.microsoft/r/vPKFDLfsUV</a></p>
<p><a href="https://forms.cloud.microsoft/r/vPKFDLfsUV">https://forms.cloud.microsoft/r/vPKFDLfsUV</a></p>
<p>Terima kasih</p>
<p>Prodi S-1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2025-11-26',
                'updated_at' => '2025-11-26',
            ],
            'Informasi Registrasi, Pembayaran, Undur Diri, Cuti, Aktivasi Mahasiswa Semester Genap 2526' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<p>1️⃣ Periode Registrasi Semester Genap 2526 melalui sirama, mekanisme jadwal registrasi tiap angkatan <a href="https://baa.telkomuniversity.ac.id/">https://baa.telkomuniversity.ac.id/</a> menu registrasi</p>
<p>🏓 Mahasiswa Reguler 2-12 Februari 2026 di SIRAMA. Periode generate cetak KSM 18-20 Februari 2026 oleh BSLA (dengan syarat sudah registrasi MK dan status pembayaran LUNAS/CREDIT)</p>
<p>🏓 Mahasiswa PJJ 2-12 Februari 2026 di SIRAMA. Periode generate KSM 18 - 20 Februari 2026 (dengan syarat sudah registrasi MK dan status pembayaran LUNAS/CREDIT)</p>
<p>2️⃣ Pengajuan CUTI/UNDUR DIRI paling lambat 6 Maret 2026 pukul 16.30 WIB melalui Igracias. Jika pengajuan melebihi tanggal tersebut maka mahasiswa dianggap MANGKIR. Panduan CUTI dan UNDIR <a href="https://linktr.ee/laaksoc">https://linktr.ee/laaksoc</a> menu Pengajuan Cuti Akademik/Pengajuan Undur Diri Mahasiswa.</p>
<p>3️⃣ Jadwal pembayaran BPP</p>
<p>🥎 Mahasiswa Reguler</p>
<p>💧 Tahap I: 2 Jan - 1 Feb 2026</p>
<p>💧 Tahap II: 14 Feb - 1 Mar 2026</p>
<p>🥎 Mahasiswa PJJ 16-20 Februari 2026 (Periode generate tagihan BPP 13 Februari 2026)</p>
<p>SK tarif dapat diakses melalui website <a href="https://finance.telkomuniversity.ac.id/">https://finance.telkomuniversity.ac.id/</a> atau <a href="https://tel-u.ac.id/sktarifbpp">https://tel-u.ac.id/sktarifbpp</a></p>
<p>4️⃣ Bagi mahasiswa yang mengalami kendala ekonomi, bisa mengajukan penundaan BPP (akses melalui <a href="https://satu.telkomuniversity.ac.id">https://satu.telkomuniversity.ac.id</a> atau <a href="https://situ-keu.telkomuniversity.ac.id/">https://situ-keu.telkomuniversity.ac.id/</a> atau <a href="https://bit.ly/TelUCare">https://bit.ly/TelUCare</a> login SSO) dengan jadwal pengajuan 2 Januari s.d 1 Februari 2026. (Mahasiswa yang mengajukan penundaan pembayaran melalui Tel U Care hanya diperbolehkan yang memiliki memiliki maksimal 2 semester tunggakan sebelumnya).</p>
<p>🍭 Dokumen persyaratan penundaan BPP dapat dilihat pada link tersebut, mohon dapat disiapkan sesegera mungkin supaya registrasi berjalan dengan baik.</p>
<p>5️⃣ Apabila ada kendala terkait pembayaran bisa kontak layanan keuangan mahasiswa di nomor whatsapp : 082214161954 (chat only) dan untuk kendala teknis pada sistem Tel U Care dapat menghubungi helpdesk Pusat Teknologi Informasi (PuTI) Universitas Telkom di nomor 082319949941 (Chat WA).</p>
<p>6️⃣ Bagi mahasiswa yang status mahasiswanya non-aktif (dikarenakan semester sebelumnya CUTI/MANGKIR) maka bisa mengajukan permohonan aktivasi melalui link <a href="https://linktr.ee/laaksoc">https://linktr.ee/laaksoc</a> (pilih menu Permohonan Aktif Kuliah) kemudian bisa melakukan registrasi setelah status mahasiswa menjadi Aktif Kembali. Ajuan aktivasi maksimal tanggal 2 Maret 2026.</p>
<p>7️⃣ Masa Perubahan Rencana Studi (PRS) 2-6 Maret 2026.</p>
<p>8️⃣ Registrasi dinyatakan selesai apabila sudah CETAK KSM. Syarat bisa cetak KSM adalah sudah registrasi MK dan status pembayaran LUNAS/CREDIT.</p>
<p>Demikian. Terimakasih.</p>
<p>LAA FIF🙏🙂</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2026-01-27',
                'updated_at' => '2026-02-02',
            ],
            '[Perpanjangan] Pengajuan Penundaan Pembayaran TEL-U CARE' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<p>🔖 Pengajuan penundaan pembayaran (Tel U Care) diperpanjang sampai tanggal 4 Maret 2026 (mohon dipastikan bahwa berkas yg diupload sudah sesuai, supaya tidak ada revisi).</p>
<p>🔖 Mahasiswa yang mengajukan penundaan pembayaran melalui Tel U Care hanya diperbolehkan yang memiliki maksimal 2 semester tunggakan sebelumnya. Aplikasi Tel U Care dapat diakses melalui <a href="https://satu.telkomuniversity.ac.id">https://satu.telkomuniversity.ac.id</a> atau <a href="https://bit.ly/TelUCare">https://bit.ly/TelUCare</a></p>
<p>🔖 Apabila ada kendala aplikasi dapat menghubungi melalui whatsapp IT Service Desk PUTI : 082319949941.</p>
<p><strong>🔖 Berkas yang harus disiapkan:</strong></p>
<ol>
<li>Surat permohonan yang ditandatangani oleh orangtua/wali (format bisa diunduh melalui telu care).</li>
<li>Bukti bayar awal minimal sebesar 30% dari total tagihan. Pembayaran melalui VA BNI nomor: 832101+[NIM].</li>
<li>Mutasi rekening tabungan Orang Tua untuk 3 (tiga) bulan terakhir.</li>
<li>Rekening listrik untuk 1 (satu) bulan terakhir.</li>
<li>Berkas pendukung lainnya.</li>
</ol>',
                'image' => null,
                'created_at' => '2026-01-27',
                'updated_at' => '2026-02-02',
            ],
            'Informasi Jadwal Pembayaran dan Tata Cara Pembayaran pada Sistem Baru' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<p>1️⃣ Jadwal Pembayaran (Semester Genap 2526)</p>
<p>💧 Tahap I: 2 Jan - 1 Feb 2026</p>
<p>💧 Tahap II (PRS): 14 Feb - 1 Mar 2026</p>
<p>Mhs harap membayar dalam rentang waktu tersebut agar tdk terjadi keterlambatan/kendala administrasi. Mhs yg membayar setelah tanggal 1 Feb 2026 diarahkan untuk registrasi di masa PRS.</p>
<p>2️⃣ Tata Cara Pembayaran pada Sistem Baru (New Digital Payment System)</p>
<p>🏀 Mhs login ke iGracias → Menu Pembayaran</p>
<p>🏀 Mhs cek jumlah Tagihan yang akan dibayar dan klik tombol bayar</p>
<p>🏀 Sistem akan menampilkan beberapa Metode Pembayaran</p>
<p>🏀 Pembayaran dapat dilakukan melalui: VA Mandiri, VA BNI, VA BJB, VA BSI, QRIS, Kartu Kredit, E- Wallet. Bagi yang tidak memiliki rekening Bank tersebut dapat melakukan transfer antar Bank melalui salah satu VA.</p>
<p>🏀 Setelah pembayaran dilakukan, status pembayaran akan otomatis terupdate di sistem secara real-time.</p>
<p>3️⃣ Dukungan &amp; Layanan Bantuan</p>
<p>Helpdesk Keuangan</p>
<p>🔖 Telepon: 08112162204</p>
<p>🔖 WA: 082214161954</p>
<p>🔖 Jam Layanan: 09.00 s/d 15.30</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2026-01-27',
                'updated_at' => '2026-01-27',
            ],
            'Update Informasi Registrasi S-1 IF Genap TA 2025/26' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<p>Prodi akan melakukan update berkala terkait tambahan informasi atau update dari registrasi semester Genap TA 2025/26. Silahkan selalu rutin untuk membaca update berkala pada halaman ini</p>
<p>Materi Registrasi Genap TA 2025/26 dapat diakses pada halaman <a href="https://info-bif.telkomuniversity.ac.id/links">https://info-bif.telkomuniversity.ac.id/links</a> (Section 2)</p>
<h2>Daftar mata kuliah yang ditawarkan</h2>
<p>Mahasiswa <strong>HANYA DIPERBOLEHKAN</strong> mengambil mata kuliah dengan prefix kelas IF (khusus MHS Reguler dan Internasional) atau IFX (khusus MHS Ekstensi). Terdapat di Excel Daftar Mata Kuliah yang sudah dishare di info-bif. <strong>MOHON TIDAK MENGAMBIL KELAS MK yang TIDAK BERAWALAN dengan IF/IFX.</strong> Hal ini karena kelas tersebut merupakan kelas dari <strong>FAKULTAS LAIN YANG TIDAK BISA DIAKUI SEBAGAI MATA KULIAH DI PRODI S-1 IF.</strong></p>
<p>Contoh: SE-05, AK-FINON</p>
<h2>Informasi Umum tekait Kerja Praktik (KP) dapat diakses di <a href="https://info-bif.telkomuniversity.ac.id/post/27">https://info-bif.telkomuniversity.ac.id/post/27</a></h2>
<p>Mahasiswa yang mengambil SKS KP di Genap TA 2025/26 adalah dengan ketentuan berikut:</p>
<ol>
<li>Mahasiswa yang belum menyelesaiakan KP di Ganjil TA 2025/26 (administrasi atau penilaian), atau</li>
<li>Mahasiswa yang melaksanakan KP dari skema Konversi SKS, atau</li>
<li>Mahasiswa yang tidak ada kuliah onsite dan pelaksaannya di semester berjalan.</li>
</ol>
<h2>Daftar mata kuliah MK S-2 Informatika yang TIDAK BOLEH DIAMBIL oleh mahasiswa S-1 Informatika (hanya peserta FASTTRACK S-2 IF).</h2>
<ol>
<li>CAK61AB4 - DESAIN ALGORITMA LANJUT &gt;&gt; S2IF-49-01</li>
<li>CAK62AB4 - KECERDASAN BUATAN LANJUT &gt;&gt; S2IF-48-01</li>
<li>CAK64AB4 - PEMODELAN &amp; OPTIMASI LANJUT &gt;&gt; S2IF-49-01</li>
<li>CAK65AA3 - PROPOSAL TESIS &gt;&gt; S2IF-49-06</li>
<li>CAK65AA3 - PROPOSAL TESIS &gt;&gt; S2IF-49-01</li>
<li>CAK65AA3 - PROPOSAL TESIS &gt;&gt; S2IF-49-04</li>
<li>CAK65AA3 - PROPOSAL TESIS &gt;&gt; S2IF-49-05</li>
<li>CAK65AA3 - PROPOSAL TESIS &gt;&gt; S2IF-49-02</li>
<li>CAK66FB3 - PENGENALAN SOSIO INFORMATIKA DAN ETIKA &gt;&gt; S2IF-49-01</li>
<li>CAK69GB3 - PRINSIP SAINS DATA &gt;&gt; S2IF-49-01</li>
<li>CAK6BHB3 - PENGENALAN INFRASTRUKTUR DAN LAYANAN KOMPUTASI &gt;&gt; S2IF-49-01</li>
<li>CAK6EIB3 - TREN PADA REKAYASA PERANGKAT LUNAK &gt;&gt; S2IF-49-01</li>
<li>CAK6HJB3 - TREN PADA VISI KOMPUTER &gt;&gt; S2IF-49-01</li>
</ol>
<h2>MAHASISWA YANG DIPERBOLEHKAN mengambil hanya berikut ini:</h2>
<ul>
<li>Jevon Sebastian (1301223391)</li>
<li>Mohammad Alvinanda Kurniawan (1301223004)</li>
<li>Krisna Aditya David Putra (1301223132)</li>
<li>Haidar Sayyid Ramadhan (1301223105)</li>
<li>Muhammad Shafa Praramadhana (1301223055)</li>
</ul>
<h2>Update jadwal kelas Proposal HUMIC sudah ditambahkan sama dengan kelas Proposal lainnya. Kamis jam 13:30 WIB - Selesai.</h2>
<p>Penambahan Jadwal Mata Kuliah Algoritma dan Pemrograman 2, yang awalnya 3 jam 1 pertemuan dalam satu pekan diubah menjadi 2 jam 2 pertemuan dalam satu pekan. Jadwal tambahan dapat dilihat pada SIRAMA.</p>
<p>Mohon maaf kepada mahasiswa yang bentrok, Prodi sudah melakukan pemindahan kelas mata kuliah untuk memastikan tidak ada yang bentrok dengan adanya penambahan pertemuan perkuliahan ini. Berikut adalah daftar mahasiswa bukan angkatan 2025 yang Prodi pindahkan kelas mata kuliahanya.</p>
<ul>
<li>103012400190 ORLANDO SILAS DAVINCCI KAMBU &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012300413 MUHAMMAD KEVIN NUGRAHA &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400340 JUNIOR PATRA MAMAHIT &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400203 RAVADANDI OUDENY PUNCA &gt;&gt; ⚠️ DIPINDAH KELAS KALKULUS LANJUT</li>
<li>103012400285 RAJA JAMEL PANGLIMA &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400083 RAFA ANDHARA RIZQI &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400150 AFRICA NURHANRAFIF &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400278 GANDA SETYA RAMADHANA &gt;&gt; ⚠️ DIPINDAH KELAS INTERAKSI MANUSIA DAN KOMPUTER</li>
<li>103012400309 ACHMAD FADILLAH MUCHSON &gt;&gt; ⚠️ DIPINDAH KELAS INTERAKSI MANUSIA DAN KOMPUTER</li>
<li>103012440005 MUHAMMAD GAVIN SATRIO PRABASWARA &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400075 MUHAMMAD DANISH ABBY IHSAN &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012440022 ANITA WAHYUNINGSIH MARDIANA &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012300534 ADAM MUHAMMAD ROBBANI &gt;&gt; ⚠️ DIPINDAH KELAS ALGORITMA DAN PEMROGRAMAN 2</li>
<li>103012330440 DANISH WAHYU IBRAHIM &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012430006 ANDREAN ANGGIANO DIDANE &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400160 MUHAMMAD RIZKY FADLY MANSYURI &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012300454 FRANSISKUS PARSAORAN SITUMORANG &gt;&gt; ⚠️ DIPINDAH KELAS ALGORITMA DAN PEMROGRAMAN 2 DAN MATRIK RUANG VEKTOR</li>
<li>103012430035 RIFKY SYAPUTRA &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400084 SEPTYA NOER FAUZIYAH &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400355 JIHAN PUTRI FERDINA &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012400311 ZALIKA ZAHRA TASYIFA POERBA &gt;&gt; 🚸 WGTIK DIPERBOLEHKAN BENTROK, KARENA HANYA 1 JADWAL</li>
<li>103012330168 IZZRA HILAL ADITYO &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400128 MUHAMMAD RAFI RADITIA LATIF &gt;&gt; TIDAK BENTROK ✅</li>
<li>103012400167 BAGAS SATRIO DIWANTOKO &gt;&gt; ⚠️ DIPINDAH KELAS PEMROGRAMAN BERORIENTASI OBJEK</li>
</ul>
<h2>Update Jadwal Kelas Mata Kuliah</h2>
<p>PENGOLAHAN CITRA DIGITAL (CAK4OBB3) KELAS IF-46-DSIS.02 &gt;&gt; SENIN, 09:30:00 - 12:30:00, TULT 0706</p>
<h2>Daftar mata kuliah yang diperbolehkan bentrok</h2>
<ul>
<li>Wawasan Global TIK (Online)</li>
<li>Computing Project (Onsite 1x di akhir pada saat ekshibisi)</li>
<li>Informatika untuk Masyarakat (Online)</li>
</ul>
<h2>Kelas Internasional</h2>
<p>Apabila ada kelas reguler yang akan diambil oleh mahasiswa, mohon menghubungi via Dosen Wali.</p>
<h2>Mata Kuliah Khusus WRAP REARCHSHIP CoE HUMIC</h2>
<p>Mata kuliah berikut ini khusus untuk mahasiswa yang berencana mengikuti program WRAP RESEARCSHIP di CoE HUMIC:</p>
<ul>
<li>CAK4EAB2-PENULISAN PROPOSAL | IF-47-HUMIC</li>
<li>CAK4ZEB3-WRAP RESEARCHSHIP HUMIC2: APLIKASI BERBASIS SELULER UNTUK TELEMEDICINE | IF-46-WRAP.HUMIC</li>
<li>CAK41EB3-WRAP RESEARCHSHIP HUMIC3: KECERDASAN BUATAN TERAPAN DALAM TELEMEDICINE | IF-46-WRAP.HUMIC</li>
<li>CAK4YEB3-WRAP RESEARCHSHIP HUMIC1: APLIKASI BERBASIS WEB UNTUK TELEMEDICINE | IF-46-WRAP.HUMIC</li>
</ul>
<p>Mahasiswa tidak bisa mengambil secara sembarangan atau tanpa pertimbangan khusus, misalnya karena sudah kehabisan mata kuliah pilihan. Mohon untuk menghubungi CoE HUMIC.</p>
<p>Prodi tidak bertanggung jawab apabila mahasiswa yang mengambil topik tersebut ternyata tidak sesuai dengan topik yang diminati oleh mahasiswa, karena memang kegiatan WRAP tersebut berada di bawah tanggung jawab CoE HUMIC, bukan Prodi S-1 IF.</p>
<p>Supaya tidak terjadi kesalahan dalam pengambilan topik pada WRAP ini, silahkan menghubungi contact person Mas Raya Taufik ‪+62 821‑5394‑1209‬.</p>
<p>Video Profil CoE HUMIC Engineering <a href="https://youtu.be/AwZCCgPg3aw">https://youtu.be/AwZCCgPg3aw</a></p>
<h2>Administrasi Registrasi</h2>
<p><a href="https://info-bif.telkomuniversity.ac.id/post/25">Informasi Jadwal Pembayaran dan Tata Cara Pembayaran pada Sistem Baru</a></p>
<p><a href="https://info-bif.telkomuniversity.ac.id/post/24">Pengajuan Penundaan Pembayaran TEL-U CARE</a></p>
<p><a href="https://info-bif.telkomuniversity.ac.id/post/23">Informasi Registrasi, Pembayaran, Undur Diri, Cuti, Aktivasi Mahasiswa Semester Genap 2526</a></p>
<p>Salam,</p>
<p>Prodi S-1 Informatika</p>',
                'image' => null,
                'created_at' => '2026-01-29',
                'updated_at' => '2026-02-12',
            ],
            'Informasi Umum Kerja Praktik S-1 Informatika' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<p>Teman-teman yang akan melaksanakan Kerja Praktik (KP), maka normalnya pelaksanaan KP di perusahaan dilaksanakan pada Libur transisi antara Semester Genap ke Semester Ganjil tahun ajaran baru (Pertengahan Tahun Fiskal). Pada beberapa kasus khusus, pelaksanaan KP bisa dilaksanakan pada saat semester berjalan, tetapi hal ini bisa dilakukan apabila SKS mahasiswa tinggal TA saja dan tidak ada perkuliahan onsite yang tersisa.</p>
<h2>Panduan Kerja Praktik</h2>
<p>(berisi ketentuan kapan mahasiswa dikatakan sudah layak untuk mengikut KP, dan detail administrasinya)</p>
<h2>Informasi Lengkap dari Fakultas &gt;&gt; <a href="https://linktr.ee/laaksoc">https://linktr.ee/laaksoc</a></h2>
<p>Link Penting info-bif.</p>
<h2>Pengambilan SKS Kerja Praktik</h2>
<p>(pemilihan skema di bawah ini menyesuaikan rencana studi mahasiswa, diskusikan dengan dosen wali)</p>
<ul>
<li>SKS diambil di semester Genap (awal tahun). Khusus untuk mahasiswa yang skema konversi dari Magang atau mahasiswa yang melanjutkan KP yang belum selesai administrasi pada KP di semester ganjil sebelumnya.</li>
<li>SKS diambil di semester Ganjil (pertengahan tahun). Artinya SKS diambil setelah KP selesai dilaksanakan.</li>
</ul>
<h2>Kerja Praktik vs Magang Berdampak vs Magang Mandiri</h2>
<h3>Kerja Praktik</h3>
<p>Kerja Praktik adalah mata kuliah wajib Prodi, dengan durasi pelaksanaan 1.5 hingga 2 bulan. Setiap mahasiwa pasti mengambil mata kuliah ini.</p>
<ul>
<li>Pembimbing Akademik adalah dosen yang ditugaskan Prodi.</li>
<li>Pembimbing Lapangan adalah pegawai perusahaan yang ditugaskan untuk menjadi atasan atau pembimbing selama pelaksaan KP.</li>
</ul>
<h3>Magang Berdampak (dulu MBKM)</h3>
<p>adalah program magang dari Pemerintah, yang sifatnya tidak wajib. Durasi pelaksanaan bervariasi menyesuaikan jenis program/penyelenggaranya. Terdapat mekanisme konversi SKS yang diakui sebagai mata kuliah pilihan. Konversi maksimal 12 SKS (durasi 6 bulan), beberapa program bisa dikonversi SKS sebagai KP. Magang ini harus dilakukan dengan melakukan konversi SKS di semester berjalan pelaksanaan magang. Terdapat Buku Panduan resmi yang dikeluarkan Prodi (ada di info-bif), sehingga kadang peraturannya lebih detail dan ketat dibandingkan ketentuan dari Universitas ataupun Pemerintah.</p>
<ul>
<li>Pembimbing Akademik adalah dosen wali.</li>
<li>Pembimbing Lapangan/Mentor adalah orang yang ditugaskan perusahaan untuk membimbing mahasiswa selama magang.</li>
<li>Evaluator adalah dosen yang ditugaskan prodi untuk melakukan penilaian hasil magang. Terdiri dari dua orang yaitu dosen wali dan satu orang dosen lain.</li>
</ul>
<h3>Magang Mandiri</h3>
<p>adalah magang tidak wajib, seperti magang berdampak. Bedanya adalah mahasiswa mencari sendiri tempat magang dan di luar program Magang Berdampak. Durasi waktu bervariasi dan bisa dikonversi sebagai SKS KP, apabila bidang pekerjaan sesuai dengan Buku Panduan KP FIF dan durasi tidak kurang dari batas minimal pelaksanaan KP. Magang mandiri boleh tidak konversi SKS.</p>
<p>Pembimbing sama dengan KP apabila dikonversi menjadi KP.</p>
<h2>Persamaan ketiganya adalah merupakan kegiatan intership yang mana selama pelaksanaan hingga selesai, status mahasiswa adalah aktif (bukan alumni karena baru lulus/sudah lama lulus, bukan cuti ataupun undur diri).</h2>
<p>Karena durasi bervariasi, mohon dipastikan magang tidak mengganggu perkuliahan di Prodi yang dilaksanakan secara onsite.</p>
<p>Notes: Informasi lebih lanjut silahkan baca buku Panduan KP FIF dan Panduan Magang Berdampak S-1 IF.</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2026-02-02',
                'updated_at' => '2026-02-03',
            ],
            'Informasi Final Registrasi & Perubahan Rencana Studi' => [
                'subtitle' => 'Registrasi Semester Genap TA 2025/26',
                'body' => '<h2>CETAK KSM</h2>
<p>Mohon dipastikan di IGRACIAS bahwa DAFTAR MK REGISTRASI pada menu Registrasi &gt;&gt; Registrasi &gt;&gt; Registrasi Mata Kuliah SAMA dengan KSM YANG BERLAKU di Registrasi &gt;&gt; Registrasi &gt;&gt; Arsip KSM, baik kelas, mata kuliah dan jumlah SKS</p>
<p>Apabila berbeda mohon menghubungi Prodi via Dosen Wali, mengingat hari ini adalah hari terakhir PRS.</p>
<h2>Tel-U Care</h2>
<p>Bagi yang mengajukan Penundaan BPP (cicilan) mohon dipastikan progress sudah 100% untuk bisa melakukan Registrasi. Lakukan pengecekan Tel-U Care secara berkala, untuk memastikan perbaikan/revisi dilakukan segera.</p>
<h2>CUTI &amp; UNDIR</h2>
<p>Bagi teman-teman yang mengajukan Cuti atau Undur Diri, mohon dipastikan bahwa Nama Orang Tua di KTP sama dengan nama Orang Tua di Surat Pernyataan Cuti/Undir. Pastikan juga Dosen Wali telah melakukan Approval. Ketidak sesuaian ini bisa mengakibatkan pengajuan ditolak.</p>
<p>Apabila ada kendala upload perbaikan silahkan mengirimkan berkas softfile ke LAA via Dosen Wali.</p>
<h2>PENUTUPAN KELAS</h2>
<p>Berikut adalah DAFTAR MATA KULIAH YANG DITUTUP dikarekan jumlah mahasiswa tidak mencapai kuota minimum pembukaan kelas mata kuliah. Prodi sudah menghubungi via Dosen Wali.</p>
<table>
<thead>
<tr><th>NO</th><th>NIM</th><th>NAMA</th><th>DOSWAL</th><th>KODE</th><th>MATA KULIAH</th><th>KELAS ASAL</th><th>KELAS TUJUAN</th></tr>
</thead>
<tbody>
<tr><td>1</td><td>103012400209</td><td>BENEDICTUS DANIEL WIDIYATMOKO</td><td>APK</td><td>CAK3BAB3</td><td>IMPLEMENTASI DAN PENGUJIAN PERANGKAT LUNAK</td><td>IF-48-GABUP.04</td><td>IF-48-GABUP.05</td></tr>
<tr><td>2</td><td>103012430036</td><td>MUHAMMAD RIFKY ALFAJRI</td><td>BMG</td><td>CAK3BAB3</td><td>IMPLEMENTASI DAN PENGUJIAN PERANGKAT LUNAK</td><td>IF-48-GABUP.04</td><td>IF-48-GABUP.01</td></tr>
<tr><td>3</td><td>103012300373</td><td>VITO NATAEL REINHARD PALEBANGAN</td><td>DNS</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.04</td><td>IF-47-GABUP.02</td></tr>
<tr><td>4</td><td>103012330440</td><td>DANISH WAHYU IBRAHIM</td><td>LDS</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.04</td><td>IF-47-GABUP.02</td></tr>
<tr><td>5</td><td>103012300096</td><td>DAMAR MUHARRAMDYA</td><td>AUB</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.06</td><td>IF-47-GABUP.02</td></tr>
<tr><td>6</td><td>103012300299</td><td>SYAUQI NURFIKRI RAHMAN</td><td>DYA</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.06</td><td>IF-47-GABUP.02</td></tr>
<tr><td>7</td><td>103012300348</td><td>DHAFIN GHIFFARY</td><td>DYA</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.06</td><td>IF-47-GABUP.02</td></tr>
<tr><td>8</td><td>103012330370</td><td>FADHIL ABITHYASA EFFENDI</td><td>DYA</td><td>UBKXACB2</td><td>KEWARGANEGARAAN</td><td>IF-47-GABUP.06</td><td>IF-47-GABUP.02</td></tr>
<tr><td>9</td><td>103012300072</td><td>HANIF HAIDAR FATHIN MUMTAZ</td><td>IND</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.05</td><td>IF-47-GAB.03</td></tr>
<tr><td>10</td><td>103012300218</td><td>CAESAR GIAN INDRARIZKY</td><td>IND</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.05</td><td>IF-47-GAB.03</td></tr>
<tr><td>11</td><td>103012300233</td><td>DANANG SETIYOADI</td><td>IND</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.05</td><td>IF-47-GAB.03</td></tr>
<tr><td>12</td><td>103012300276</td><td>RESAVA HAIDAR ADHITIA</td><td>IND</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.05</td><td>IF-47-GAB.03</td></tr>
<tr><td>13</td><td>1301220250</td><td>MUHAMMAD RIFKI HIDAYATULLAH</td><td>EMJ</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.05</td><td>IF-47-GAB.03</td></tr>
<tr><td>14</td><td>103012300065</td><td>HIKMAT ARIF NUGRAHA</td><td>SYP</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>15</td><td>103012300093</td><td>YUKIE RAMADHANI KIYOSHI</td><td>SYP</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>16</td><td>103012300165</td><td>MUHAMMAD ARYA DWI KESUMA</td><td>EAR</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>17</td><td>103012300481</td><td>MUHAMMAD ZAINUL ALIF</td><td>SYP</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>18</td><td>103012330138</td><td>NUR AISYA JAMIL</td><td>SYP</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>19</td><td>1301210539</td><td>VIONI MARYETA GRADIELA MARPAUNG</td><td>BDP</td><td>CAK3FAB3</td><td>MANAJEMEN PROJEK TIK</td><td>IF-47-GAB.02</td><td>IF-47-GAB.03</td></tr>
<tr><td>20</td><td>103012580022</td><td>FAZRUL RIDHA ALLIANDRE</td><td>MDS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IFX-49-TRANS.02</td><td>IF-47-GAB.04</td></tr>
<tr><td>21</td><td>103012580030</td><td>ALIF MAHTUM ALFAIDZ</td><td>MDS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IFX-49-TRANS.02</td><td>IF-47-GAB.04</td></tr>
<tr><td>22</td><td>103012580058</td><td>MUHAMMAD</td><td>MDS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IFX-49-TRANS.02</td><td>IF-47-GAB.04</td></tr>
<tr><td>23</td><td>103012300058</td><td>RASHAQA NASHWAN MOYAL</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>24</td><td>103012300112</td><td>MAULIANI RAHMA FAZWAT</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>25</td><td>103012300201</td><td>HAFIZH MARFIANSYAH PUTRA</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>26</td><td>103012300220</td><td>ALIF RAHMAN RASYAD ADIL</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>27</td><td>103012300234</td><td>RYAN GHAFRAN LUHUR</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>28</td><td>103012300238</td><td>AZZAHRA INDAH</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>29</td><td>103012330238</td><td>DESHIFA CANTIKA DONDOL</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
<tr><td>30</td><td>103012580020</td><td>GODWIN HALLEY WATTIMENA</td><td>DS</td><td>CAK3KAB3</td><td>TATA TULIS ILMIAH</td><td>IF-47-GAB.06</td><td>IF-47-GAB.05</td></tr>
</tbody>
</table>
<p>Terima kasih.</p>
<p>Prodi S-1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2026-03-05',
                'updated_at' => '2026-03-05',
            ],
            'Pengumuman Informatika untuk Masyarakat' => [
                'subtitle' => 'Informatika untuk Masyarakat Genap 2025/26',
                'body' => '<p>Berikut kami informasikan terkait hasil plotting Dosen Pembimbing IUM</p>
<p><a href="https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQCwYwjKjtNiTrpZgNJ6z7itAc4L62ujW8oy3EK3iPe9vnE?e=is6T4h">https://telkomuniversityofficial-my.sharepoint.com/:x:/g/personal/bif_telkomuniversity_ac_id/IQCwYwjKjtNiTrpZgNJ6z7itAc4L62ujW8oy3EK3iPe9vnE?e=is6T4h</a></p>
<p>Kepada masing-masing kelompok untuk menunjuk ketua kelompok, membuat group komunikasi dan menuliskan di excel plotting (akses SSO 365)</p>
<p>Link ini akan dishare kepada dosen pembimbing untuk mengetahui hasil plotting. Dosen pembimbing mungkin akan join group atau meminta untuk dikontak secara personal. Mohon bijak dalam mengontak dosen (hanya di hari dan jam kerja).</p>
<p>Kontak dosen dapat diakses melalu bot fif di topic group yang sudah disediakan.</p>
<p>Informasi lain terkait IUM dapat diakses melalui INFO-BIF menu LINK PENTING. (<a href="https://info-bif.telkomuniversity.ac.id/links">https://info-bif.telkomuniversity.ac.id/links</a> section 5)</p>
<p>Update Pengumpulan berkas akan dilakukan via LMS, mohon dipastikan untuk join LMS.</p>',
                'image' => null,
                'created_at' => '2026-03-10',
                'updated_at' => '2026-03-11',
            ],
            'Magang Mandiri Fakultas Informatika' => [
                'subtitle' => 'Langkah Pengajuan Magang Mandiri Fakultas Informatika',
                'body' => '<p>Hallo mahasiswa Prodi S1 Fakultas Informatika😍</p>
<p>Mau mengikuti magang mandiri???!!!!🙌</p>
<p>Yuk ikuti langkah-langkahnya🔖</p>
<p>1️⃣ Mengisi Form Validasi Dosen Wali untuk Pembuatan Surat Pengantar Magang Mandiri. Format Form Validasi dosen wali dapat diunduh pada link <a href="https://tel-u.ac.id/formvalidasidoswalmagangmandiri">https://tel-u.ac.id/formvalidasidoswalmagangmandiri</a></p>
<p>2️⃣ Upload Form Validasi Dosen wali yang sudah ditandatangani lengkap oleh Mahasiswa, Dosen Wali dan Ka. Prodi melalui link <a href="https://tel-u.ac.id/ajuanmagangmandiri">https://tel-u.ac.id/ajuanmagangmandiri</a></p>
<p>3️⃣ Input Ajuan Surat Pengantar Magang ke aplikasi TOSS <a href="https://toss.telkomuniversity.ac.id/">https://toss.telkomuniversity.ac.id/</a> (Login SSO)</p>
<p>4️⃣ Ajuan Surat Pengantar Mangang di TOSS akan di-approve jika mahasiwa sudah upload form validasi dosen wali.</p>
<p>📕 CP Pak Asep Fitri +62 822-1913-0102</p>
<p>Salam,</p>
<p>FIF</p>',
                'image' => null,
                'created_at' => '2026-04-02',
                'updated_at' => '2026-04-02',
            ],
            'Timeline Sidang TA dan Yudisium Genap 2526 Prodi S-1 Informatika' => [
                'subtitle' => 'Tugas Akhir Genap 2526',
                'body' => '<p>📆 [TIMELINE SIDANG TA &amp; YUDISIUM GENAP 25/26 S-1 INFORMATIKA]</p>
<p>Kpd mahasiswa TA genap 25/26 S-1 Informatika, berikut timeline sidang TA dan yudisium:</p>
<p>tel-u.ac.id/2526-2timelinetas1if</p>
<p>🎯 Informasi detail syarat berkas Tugas Akhir &amp; Yudisium dapat diakses pada link: <a href="https://linktr.ee/laaksoc">https://linktr.ee/laaksoc</a> (pilih menu Panduan TA &amp; Proposal)</p>
<p>Salam,</p>
<p>Prodi S-1 Informatika</p>',
                'image' => null,
                'created_at' => '2026-04-02',
                'updated_at' => '2026-04-02',
            ],
            'Kerja Praktik 2026' => [
                'subtitle' => 'Kerja Praktik Genap 2526',
                'body' => '<p>Hallo rekan-rekan mahasiswa pejuang KP🤩</p>
<p>Mau KP tapi masih bingung tahapannya apa saja??!! 🤔</p>
<p>Tenang, temen-temen bisa akses link berikut:</p>
<p>🍔 Tahapan Persiapan KP <a href="https://tel-u.ac.id/tahapanpersiapankp">https://tel-u.ac.id/tahapanpersiapankp</a></p>
<p>🍟 Tahapan Pelaksanaan KP <a href="https://tel-u.ac.id/tahapanpelaksanaankp">https://tel-u.ac.id/tahapanpelaksanaankp</a></p>
<p>🍕 Tahapan Setelah Selesai KP <a href="https://tel-u.ac.id/tahapansetelahselesaikp">https://tel-u.ac.id/tahapansetelahselesaikp</a></p>
<p>Informasi lengkap, silahkan akses🍒 <a href="https://linktr.ee/laaksoc">https://linktr.ee/laaksoc</a> menu Informasi Kerja Praktik (KP)</p>
<p>Bedanya Kerja Praktik vs Magang Berdampak vs Magang Mandiri <a href="https://info-bif.telkomuniversity.ac.id/post/27">https://info-bif.telkomuniversity.ac.id/post/27</a></p>
<p>Yuk, segera urus KP mu 🏃‍♂️‍➡️</p>
<p>Salam, LAAK FIF</p>',
                'image' => null,
                'created_at' => '2026-04-02',
                'updated_at' => '2026-04-10',
            ],
            'Panduan Teknis Seminar Internal Onsite Mahasiswa Prodi S-1 Informatika' => [
                'subtitle' => 'Tugas Akhir',
                'body' => '<p>Kpd mahasiswa TA genap 2526, kami menginformasikan bahwa mulai semester Genap 2025/2026, seminar Internal di lingkungan Program Studi S-1 Informatika dilaksanakan (by default) secara ONSITE. Kebijakan ini dimaksudkan untuk mendukung para mahasiswa peserta seminar agar dapat memberikan yang terbaik, mulai dari persiapan hingga pemaparan materi.</p>
<p>Pelaksanaan teknis Seminar Internal ini adalah tim dosen khusus yang dibentuk oleh Program Studi melalui Surat Tugas.</p>
<p>🎯 Informasi detail panduan teknis seminar internal, dapat diakse pada link berikut: <a href="https://tel-u.ac.id/mhs-panduanseminarinternal-s1if">https://tel-u.ac.id/mhs-panduanseminarinternal-s1if</a></p>
<p>Terima kasih,</p>
<p>Prodi S1 Informatika</p>',
                'image' => 'images/placeholder.png',
                'created_at' => '2026-04-23',
                'updated_at' => '2026-04-23',
            ],
        ];

        foreach ($posts as $title => $data) {
            $timestamps = [
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
            ];
            unset($data['created_at'], $data['updated_at']);

            $post = Post::updateOrCreate(
                ['title' => $title],
                $data
            );

            Post::whereKey($post->id)->update($timestamps);
        }
    }
}