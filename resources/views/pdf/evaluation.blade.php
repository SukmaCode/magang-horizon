<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Evaluasi Magang - {{ $mahasiswa['nama_lengkap'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .page {
            padding: 30px 40px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px double #333;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #555;
        }

        /* Identity Sections */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            /* margin-bottom: 2px; */
            padding-bottom: 4px;
            border-bottom: 1px solid #ddd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .identity-container{
            width: 100%;
        }
        .identity-container::after {
            content: "";
            display: table;
            clear: both;
        }
        .identity-box{
            width: 48%;
            float: left;
        }
        .identity-box:last-child {
            float: right;
        }
        .identity-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .identity-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .identity-table td.label {
            width: 140px;
            font-weight: bold;
            color: #555;
        }
        .identity-table td.separator {
            width: 10px;
        }

        /* Scores Table */
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .scores-table th {
            background-color: #f0f0f0;
            border: 1px solid #999;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        .scores-table th.center {
            text-align: center;
        }
        .scores-table td {
            border: 1px solid #999;
            padding: 7px 12px;
            font-size: 11px;
        }
        .scores-table td.center {
            text-align: center;
        }
        .scores-table td.number {
            text-align: center;
            width: 40px;
        }
        .scores-table td.score {
            text-align: center;
            width: 100px;
            font-weight: bold;
            font-size: 12px;
        }
        .scores-table tr.total-row {
            background-color: #f8f8f8;
        }
        .scores-table tr.total-row td {
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid #333;
        }

        /* Notes Section */
        .notes-section {
            margin-bottom: 25px;
        }
        .notes-box {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 12px 15px;
            min-height: 60px;
            font-size: 11px;
            line-height: 1.6;
            background-color: #fafafa;
        }

        /* Signature */
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 40%;
            float: right;
            text-align: center;
        }
        .signature-img {
            height: 60px;
            margin: 10px auto;
            display: block;
        }
        .signature-placeholder {
            height: 80px;
            margin: 10px 0;
            line-height: 80px;
            color: #999;
            font-style: italic;
            font-size: 10px;
        }
        .signature-line {
            width: 150px;
            border-bottom: 1px solid black;
            margin-bottom: 10px;
            /* margin-top: 20px; */
            margin-left: auto;
            margin-right: auto;
        }
        .signature-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            display: inline-block;
            padding-top: 5px;
            min-width: 180px;
        }
        .clear {
            clear: both;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="page">

        <!-- Header -->
        <div class="header">
            <h1>Lembar Evaluasi Magangss</h1>
            <h2>Internship Evaluation Form</h2>
        </div>

        <!-- Student Identity -->
        <div class="identity-container">
            <div class="identity-box">
                <div class="section-title">Identitas Mahasiswa</div>
                <table class="identity-table">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="separator">:</td>
                        <td>{{ $mahasiswa['nama_lengkap'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIM</td>
                        <td class="separator">:</td>
                        <td>{{ $mahasiswa['nim'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="separator">:</td>
                        <td>{{ $mahasiswa['prodi'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Industry Identity -->
            <div class="identity-box">
                <div class="section-title">Identitas Industri</div>
                <table class="identity-table">
                    <tr>
                        <td class="label">Nama Perusahaan</td>
                        <td class="separator">:</td>
                        <td>{{ $industri['nama_perusahaan'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="separator">:</td>
                        <td>{{ $industri['alamat'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Periode Magang</td>
                        <td class="separator">:</td>
                        <td>{{ $tanggal_mulai }} s.d {{ $tanggal_selesai }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Scores Table -->
        <div class="section-title">Tabel Penilaian</div>
        <table class="scores-table">
            <thead>
                <tr>
                    <th class="center" style="width: 40px;">No</th>
                    <th>Komponen Penilaian</th>
                    <th class="center" style="width: 100px;">Nilai (0-100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $score)
                <tr>
                    <td class="number">{{ $score['no'] }}</td>
                    <td>{{ $score['label'] }}</td>
                    <td class="score">{{ number_format($score['nilai'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" style="text-align: left; padding-right: 15px;">NILAI AKHIR (RATA-RATA)</td>
                    <td class="score" style="font-size: 14px;">{{ number_format($nilai_akhir, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Supervisor Notes -->
        <div class="notes-section">
            <div class="section-title">Catatan Supervisor</div>
            <div class="notes-box">
                {{ $catatan_supervisor }}
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Tanggal: {{ $tanggal_evaluasi }}</p>
                <p style="margin-top: 5px;">Supervisor Industri,</p>

                @if($supervisorSignatureBase64)
                    <img src="{{ $supervisorSignatureBase64 }}" class="signature-img" alt="Tanda Tangan Supervisor">
                @else
                    <div class="signature-placeholder">(Tanda tangan belum tersedia)</div>
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $supervisor_name }}</div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini di-generate secara otomatis oleh Sistem Magang Horizon &bull; {{ now()->format('d F Y H:i') }}
        </div>

    </div>
</body>
</html>
