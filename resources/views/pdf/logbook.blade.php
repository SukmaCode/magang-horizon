<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logbook Report - {{ $mahasiswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 150px;
            font-weight: bold;
        }
        .logbook-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .logbook-table th, .logbook-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        .logbook-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .signature-section {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }
        .signature-box.right {
            float: right;
        }
        .signature-img {
            height: 80px;
            margin: 10px 0;
        }
        .signature-placeholder {
            height: 80px;
            margin: 10px 0;
            line-height: 80px;
            color: #999;
            font-style: italic;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Logbook Magang</h2>
        <p>Periode: {{ $magangAktif->tanggal_mulai ? $magangAktif->tanggal_mulai->format('d M Y') : '-' }} s.d {{ $magangAktif->tanggal_selesai ? $magangAktif->tanggal_selesai->format('d M Y') : '-' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Student Name</td>
            <td>: {{ $mahasiswa->nama_lengkap }}</td>
            <td class="label">Company Name</td>
            <td>: {{ $industri ? $industri->nama_perusahaan : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Student ID</td>
            <td>: {{ $mahasiswa->nim }}</td>
            <td class="label">Company Address</td>
            <td>: {{ $industri->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Program</td>
            <td>: {{ $mahasiswa->prodi ?? '-' }}</td>
            <td class="label">Company Supervisor</td>
            <td>: {{ $industri->kontak_person ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Internship Period</td>
            <td>: {{ $periode->tanggal_buka ?? '-' }} s.d {{ $periode->tanggal_tutup ?? '-' }}</td>
            <td class="label">Academic Supervisor</td>
            <td>: {{ $dosen->nama_dosen ?? '-' }}</td>
        </tr>
    </table>

    <table class="logbook-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Check-In time</th>
                <th width="15%">Check-Out time</th>
                <th width="10%">Total Hours</th>
                <th width="30%">Remarks</th>
                <th width="25%">Supervisor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logbooks as $index => $logbook)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $logbook->tanggal_waktu->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($logbook->status_presensi->value ?? '') }}</td>
                <td>Disetujui</td>
                <td>{{ $logbook->kegiatan }}</td>
                <td>{{$logbook->is_approved_industri->value ?? '-'}}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada logbook yang disetujui.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Dosen Pembimbing</p>
            @if($dosenSignatureBase64)
                <img src="{{ $dosenSignatureBase64 }}" class="signature-img" alt="Tanda Tangan Dosen">
            @else
                <div class="signature-placeholder">(Belum ada tanda tangan)</div>
            @endif
            <p><strong>{{ $dosen ? $dosen->nama : '-' }}</strong><br>NIDN. {{ $dosen ? $dosen->nidn : '-' }}</p>
        </div>

        <div class="signature-box right">
            <p>Menyetujui,<br>Supervisor Industri</p>
            @if($industriSignatureBase64)
                <img src="{{ $industriSignatureBase64 }}" class="signature-img" alt="Tanda Tangan Supervisor">
            @else
                <div class="signature-placeholder">(Belum ada tanda tangan)</div>
            @endif
            <p><strong>{{ $supervisorIndustri ? $supervisorIndustri->industri->nama_perusahaan ?? $supervisorIndustri->username : '-' }}</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
