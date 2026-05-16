<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FDFDF5;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Decorative Strips */
        .strip-top-gold {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 20pt;
            background-color: #C8A400;
            z-index: 15;
        }
        .strip-top-red {
            position: absolute;
            top: 8pt;
            left: 0;
            width: 100%;
            height: 35pt;
            background-color: #B22222;
            z-index: 5;
        }
        .strip-bottom-gold {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 20pt;
            background-color: #C8A400;
            z-index: 15;
        }
        .strip-bottom-red {
            position: absolute;
            bottom: 8pt;
            left: 0;
            width: 100%;
            height: 35pt;
            background-color: #B22222;
            z-index: 5;
        }

        /* Top Left Corner Triangle */
        .triangle-top-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 200pt solid #B22222;
            border-right: 200pt solid transparent;
            z-index: 10;
        }
        .triangle-bottom-right {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-bottom: 200pt solid #B22222;
            border-left: 200pt solid transparent;
            z-index: 10;
        }

        /* Logo Area */
        .logo-container {
            position: absolute;
            top: 25pt;
            left: 20pt;
            z-index: 20;
            text-align: center;
        }
        .logo-circle {
            width: 30pt;
            height: 30pt;
            background-color: #FFFFFF;
            border-radius: 50%;
            display: inline-block;
            line-height: 30pt;
            text-align: center;
            font-size: 16pt;
            color: #C8A400;
            margin-bottom: 3pt;
        }
        .logo-text {
            color: #FFFFFF;
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Main Content Container */
        .content-container {
            position: absolute;
            top: 50pt;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 5;
        }

        .main-title {
            font-weight: bold;
            font-size: 48pt;
            color: #1A237E;
            margin-top: 10pt;
            margin-bottom: 0;
            letter-spacing: 2pt;
        }
        
        .sub-title {
            font-style: italic;
            font-size: 22pt;
            color: #C8A400;
            /* margin-top: 5pt; */
            margin-bottom: 10pt;
        }

        .presented-text {
            font-size: 9pt;
            color: #666666;
            line-height: 14pt;
            margin-bottom: 25pt;
        }

        .name-container {
            margin: 0 auto 20pt auto;
            width: 450pt;
            border-top: 1px solid #CCCCCC;
            border-bottom: 1px solid #CCCCCC;
            padding: 15pt 0;
        }
        
        .recipient-name {
            font-weight: bold;
            font-style: italic;
            font-size: 30px;
            color: #B22222;
            margin: 0;
        }

        .company-name {
            font-weight: bold;
            font-size: 13pt;
            color: #444444;
            margin-bottom: 15pt;
            text-transform: uppercase;
        }

        .description-text {
            font-size: 10pt;
            color: #555555;
            line-height: 16pt;
            width: 500pt;
            margin: 0 auto;
        }

        /* Signatures Section */
        .signatures {
            position: absolute;
            bottom: 50pt;
            left: 60pt;
            right: 60pt;
        }

        .signature-col-left {
            float: left;
            width: 150pt;
            text-align: center;
        }

        .signature-col-right {
            float: right;
            width: 150pt;
            text-align: center;
        }

        .signature-space {
            height: 60pt; /* Ruang untuk tanda tangan basah */
        }

        .signature-line {
            width: 120pt;
            border-bottom: 1px solid #999999;
            margin: 0 auto 5pt auto;
        }

        .signature-label {
            font-size: 9pt;
            color: #888888;
            margin-bottom: 5pt;
        }
        
        .signature-value {
            font-size: 10pt;
            color: #333333;
            font-weight: bold;
        }
        
        .signature-title {
            font-size: 9pt;
            color: #555555;
            margin-top: 2pt;
        }
    </style>
</head>
<body>
    <!-- @php
        // Variabel (Dictionary Python-like format)
        $vars = [
            'nama_lengkap'    => $nama_mahasiswa ?? '[NAMA LENGKAP]',
            'perusahaan'      => $nama_perusahaan ?? '[NAMA PERUSAHAAN / INSTANSI]',
            'durasi'          => '1 Semester', // Durasi default/placeholder
            'divisi'          => $prodi ?? '[Divisi/Prodi]',
            'tanggal_mulai'   => $tanggal_mulai ?? '[tanggal mulai]',
            'tanggal_selesai' => $tanggal_selesai ?? '[tanggal selesai]',
            'tanggal_terbit'  => $tanggal_terbit ?? '[DD MMMM YYYY]',
            'nama_pejabat'    => '[Nama Pejabat]',
            'jabatan_pejabat' => '[Jabatan]'
        ];
    @endphp -->

    <!-- Decorative Elements -->
    <div class="strip-top-gold"></div>
    <div class="strip-top-red"></div>
    
    <div class="triangle-top-left"></div>
    <div class="triangle-bottom-right"></div>
    <div class="logo-container">
    </div>

    <div class="strip-bottom-red"></div>
    <div class="strip-bottom-gold"></div>

    <!-- Main Content -->
    <div class="content-container">
        <div class="main-title">CERTIFICATE</div>
        <div class="sub-title">of Internship</div>

        <div class="presented-text">
            THIS CERTIFICATE IS PROUDLY PRESENTED<br>
            FOR HONORABLE INTERNSHIP COMPLETION TO:
        </div>

        <div class="name-container">
            <div class="recipient-name">{{ $nama_mahasiswa }}</div>
        </div>

        <div class="company-name">{{ $nama_perusahaan }}</div>

        <div class="description-text">
            <strong>{{ $nama_mahasiswa }}</strong> telah menyelesaikan program magang di <strong>{{ $nama_perusahaan }}</strong> pada divisi <strong>{{ $prodi }}</strong>, terhitung mulai <strong>{{ $tanggal_mulai }}</strong> hingga <strong>{{ $tanggal_selesai }}</strong>, dengan dedikasi dan kinerja yang sangat baik.
        </div>
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

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-col-left">
            <div class="signature-space"></div>
            <div class="signature-line"></div>
            <div class="signature-label">DATE</div>
            <div class="signature-value">{{ $vars['tanggal_terbit'] }}</div>
        </div>
        <div class="signature-col-right">
            <div class="signature-space"></div>
            <div class="signature-line"></div>
            <div class="signature-label">SIGNATURE</div>
            <div class="signature-value">{{ $vars['nama_pejabat'] }}</div>
            <div class="signature-title">{{ $vars['jabatan_pejabat'] }}</div>
        </div>
    </div>
    

</body>
</html>
