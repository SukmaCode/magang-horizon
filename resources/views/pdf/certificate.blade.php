<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - {{ $nomor_sertifikat }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #1a1a2e;
        }

        .certificate {
            width: 100%;
            height: 100%;
            position: relative;
            padding: 60px 80px;
            box-sizing: border-box;
        }

        .border-outer {
            border: 3px solid #16213e;
            padding: 20px;
            height: calc(100% - 120px);
        }

        .border-inner {
            border: 1px solid #e94560;
            padding: 40px;
            height: calc(100% - 40px);
            text-align: center;
        }

        .header {
            font-size: 14px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 36px;
            font-weight: bold;
            color: #e94560;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }

        .subtitle {
            font-size: 16px;
            color: #333;
            margin-bottom: 30px;
        }

        .recipient {
            font-size: 28px;
            font-weight: bold;
            color: #16213e;
            margin: 20px 0 5px;
            border-bottom: 2px solid #e94560;
            display: inline-block;
            padding-bottom: 5px;
        }

        .details {
            font-size: 13px;
            color: #555;
            margin: 5px 0;
        }

        .body-text {
            font-size: 14px;
            color: #333;
            margin: 25px 0;
            line-height: 1.8;
        }

        .grade-box {
            display: inline-block;
            background: #16213e;
            color: #fff;
            padding: 10px 30px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #777;
        }

        .cert-number {
            font-size: 10px;
            color: #999;
            position: absolute;
            bottom: 70px;
            right: 100px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-outer">
            <div class="border-inner">
                <div class="header">Magang Horizon — Internship Management System</div>

                <div class="title">Sertifikat</div>
                <div class="subtitle">Penyelesaian Program Magang</div>

                <p class="body-text">Diberikan kepada:</p>

                <div class="recipient">{{ $nama_mahasiswa }}</div>
                <div class="details">NIM: {{ $nim }} | Program Studi: {{ $prodi ?? '-' }}</div>

                <p class="body-text">
                    Telah menyelesaikan program magang di<br>
                    <strong>{{ $nama_perusahaan }}</strong><br>
                    pada periode <strong>{{ $tanggal_mulai ?? '-' }}</strong> s/d <strong>{{ $tanggal_selesai ?? '-' }}</strong>
                </p>

                @if($nilai_akhir)
                    <div class="grade-box">
                        Nilai Akhir: {{ number_format($nilai_akhir, 2) }}
                    </div>
                @endif

                <div class="footer">
                    <p>Diterbitkan pada {{ $tanggal_terbit }}</p>
                </div>
            </div>
        </div>

        <div class="cert-number">No: {{ $nomor_sertifikat }}</div>
    </div>
</body>
</html>
