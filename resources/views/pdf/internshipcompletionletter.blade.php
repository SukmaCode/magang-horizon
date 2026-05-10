<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Completion Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 40px;
            color: #000;
        }
        .header {
            margin-bottom: 40px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 30px;
        }
        .timesnewroman {
            font-family: 'Times New Roman', serif;
        }
        table.student-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table.student-info td {
            vertical-align: top;
            padding: 2px 0;
        }
        .label {
            width: 150px;
        }
        .colon {
            width: 15px;
        }
        .content-section {
            margin-bottom: 20px;
        }
        ol {
            margin-top: 10px;
            margin-bottom: 20px;
            padding-left: 25px;
        }
        ol li {
            margin-bottom: 5px;
            text-align: justify;
        }
        p {
            text-align: justify;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 30px;
        }
        .signature-box {
            width: 45%;
            float: left;
            text-align: left;
        }
        .signature-box.left {
            float: left;
        }
        .signature-img {
            height: 50px;
            /* margin: 10px 0; */
        }
        .signature-placeholder {
            height: 80px;
            margin: 10px 0;
            line-height: 80px;
            color: #999;
            font-style: italic;
        }
        .signature-line {
            width: 250px;
            border-bottom: 1px solid black;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>{{ $company_name ?? 'PT. XYZ' }}</div>
        <div>{{ $company_address ?? 'Jl. Abcd No. 5 , Karawang' }}</div>
    </div>

    <div class="title timesnewroman">
        Internship Certification Letter
    </div>

    <div class="content-section timesnewroman">
        <p style="margin-bottom: 5px;">We, herewith, certify that the following student:</p>
        <table class="student-info">
            <tr>
                <td class="label">Name</td>
                <td class="colon">:</td>
                <td>{{ $student_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">University</td>
                <td class="colon">:</td>
                <td>{{ $university_name ?? 'Horizon University Indonesia' }}</td>
            </tr>
        </table>
    </div>

    <div class="content-section">
        <p class="timesnewroman">
            Has accomplished the internship as a {{ $position ?? '-' }} in the {{ $department ?? '-' }}, from {{ $start_date ?? '-' }} to {{ $end_date ?? '-' }}, with the duties and responsibilities as follows:
        </p>

        <p class="timesnewroman">{!! nl2br(e($deskripsi_tugas ?? '')) !!}</p>
    </div>

    <div class="content-section">
        <p class="timesnewroman">{!! nl2br(e($komentar_penutup ?? '')) !!}</p>
    </div>

    <div class="footer">
        <div class="timesnewroman">{{ $location_date ?? '' }}</div>
        <p class="timesnewroman">HRD <strong>{{ isset($supervisorIndustri) && $supervisorIndustri ? ($supervisorIndustri->industri->kontak_person ?? $supervisorIndustri->username) : '-' }}</strong></p>

        <div class="signature-box left">
            @if(isset($industriSignatureBase64) && $industriSignatureBase64)
                <img src="{{ $industriSignatureBase64 }}" class="signature-img" alt="Tanda Tangan Supervisor">
            @else
                <div class="signature-placeholder">(Belum ada tanda tangan)</div>
            @endif
            <div class="signature-line"></div>
            <p><strong>{{ isset($supervisorIndustri) && $supervisorIndustri ? ($supervisorIndustri->industri->nama_perusahaan ?? $supervisorIndustri->username) : '-' }}</strong></p>
        </div>
    </div>
</body>
</html>
