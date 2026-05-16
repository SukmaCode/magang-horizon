<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Akhir - {{ $studentName }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .header-table td {
            padding: 5px 8px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
        }
        ul {
            margin-top: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 30%;">Student's Name</td>
            <td>: {{ $studentName }}</td>
        </tr>
        <tr>
            <td>Institution/Company</td>
            <td>: {{ $institution }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Department:</strong> {{ $department }}</td>
        </tr>
        <tr>
            <td>Supervisor's Name</td>
            <td>: {{ $supervisorName }}</td>
        </tr>
        <tr>
            <td>Supervisor's Position / Job Title</td>
            <td>: {{ $supervisorPosition }}</td>
        </tr>
        <tr>
            <td>Working Hours</td>
            <td>: {{ $workingHours }}</td>
        </tr>
        <tr>
            <td>Internship Duration</td>
            <td>: {{ $duration }}</td>
        </tr>
    </table>

    <div class="section-title">A. Summary of the Job</div>
    <p>Describe the general purpose of your work during the internship. This may include the main project you worked on during the internship.</p>
    <p>{{ $summary }}</p>

    <div class="section-title">B. Description of Duties and Responsibilities</div>
    <p>Please explain the responsibilities that you are expected to perform during the internship period.</p>
    
    <table>
        <thead>
            <tr>
                <th>Duties/Responsibilities/Targets to be Achieved/Deadlines</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @foreach($duties as $duty)
                        <div style="font-weight: bold; margin-top: 10px;">{{ $duty['title'] ?? '' }}:</div>
                        <ul>
                            @foreach(explode("\n", $duty['description'] ?? '') as $desc)
                                @if(trim($desc) !== '')
                                    <li>{{ trim($desc) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">C. Required Knowledge and Skills</div>
    <p>Please state the required technical knowledge, skills, and attitude, that you perceive is required to perform the duties successfully</p>

    <table>
        <thead>
            <tr>
                <th style="width: 33%;">Knowledge</th>
                <th style="width: 33%;">Skills</th>
                <th style="width: 33%;">Attitude</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ol style="margin: 0; padding-left: 15px;">
                        @foreach($knowledge as $item)
                            @if(trim($item) !== '')
                                <li>{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ol>
                </td>
                <td>
                    <ol style="margin: 0; padding-left: 15px;">
                        @foreach($skills as $item)
                            @if(trim($item) !== '')
                                <li>{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ol>
                </td>
                <td>
                    <ol style="margin: 0; padding-left: 15px;">
                        @foreach($attitude as $item)
                            @if(trim($item) !== '')
                                <li>{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 30px;">Student Agreement of Responsibilities</div>
    <p>This job description has been explained to me clearly, and I understand and agree to the duties and responsibilities stated.</p>

    <div class="signature-section">
        <div class="signature-box">
            <p>Agreed by,</p>
            <br><br><br>
            <p>_______________________</p>
        </div>
        <div class="signature-box" style="float: right;">
            <p>Acknowledged by,</p>
            <br><br><br>
            <p>_______________________</p>
        </div>
    </div>

</body>
</html>
