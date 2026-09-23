<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
            padding: 0px;
            font-family: 'Times New Roman', Times, serif;
            background-image: url('{{ $bgBase64 }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            width: 100%;
            height: 100%;
        }
        .content {
            padding: 140px 80px 100px 80px;
            font-size: 14px;
            line-height: 1.6;
            text-align: justify;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            vertical-align: bottom;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h3 style="text-align: center; text-decoration: underline;">BERITA ACARA HASIL PERTEMUAN DENGAN PROGRAM STUDI</h3>
        <br>
        <p>
            Pada hari <strong>{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('l') }}</strong>, 
            tanggal <strong>{{ \Carbon\Carbon::parse($schedule->date)->locale('id')->translatedFormat('d F Y') }}</strong>, pukul <strong>{{ \Carbon\Carbon::parse($schedule->shift)->format('H:i') }} WIB</strong>,
            bertempat di <strong>{{ $schedule->meeting_room ?? '........................................' }}</strong>, telah dilaksanakan pertemuan antara <strong>{{ $schedule->requested_by }}</strong> dengan Program Studi <strong>{{ $schedule->study_program ?? 'S1 Informatika' }}</strong>. Pertemuan tersebut dihadiri oleh <strong>{{ $schedule->participants ?? 'perwakilan dari kedua belah pihak' }}</strong> dan dilaksanakan dalam rangka <strong>{{ $schedule->agenda ?? 'membahas evaluasi serta peningkatan pelaksanaan kegiatan akademik dan layanan program studi' }}</strong>.
        </p>
        <p>
            Demikian berita acara ini dibuat sebagai dokumentasi resmi atas pelaksanaan dan hasil pertemuan, serta dapat digunakan sebagaimana mestinya.
        </p>
        
        <div class="signature-table">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center; width: 50%;">
                        <br>
                        Pihak Program Studi,
                        <div class="signature-space"></div>
                        <strong>{{ $schedule->prodi_signature_name ?? '(........................................)' }}</strong><br>
                        {{ $schedule->prodi_signature_position ?? '..........................' }}
                    </td>
                    <td style="text-align: center; width: 50%;">
                        {{ $schedule->city ?? 'Bandung' }}, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
                        Pihak Terkait,
                        <div class="signature-space"></div>
                        <strong>{{ $schedule->related_party_signature_name ?? $schedule->requested_by }}</strong><br>
                        {{ $schedule->related_party_signature_position ?? '..........................' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
