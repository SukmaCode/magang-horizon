<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>University Internship Mentor Approval Sheet</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            padding: 40px;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 60px;
        }
        .content {
            text-align: justify;
            font-size: 12pt;
            margin-bottom: 80px;
        }
        .signature-section {
            text-align: center;
            width: 300px;
            margin: 0 auto;
        }
        .signature-image {
            height: 100px;
            margin: 10px 0;
            object-fit: contain;
        }
        .empty-signature {
            height: 100px;
            margin: 10px 0;
        }
        .mentor-name {
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <div class="header">
        University Internship Mentor Approval Sheet
    </div>

    <div class="content">
        In my capacity as the university internship mentor, I hereby declare that the internship final report submitted by {{ $mahasiswa_name }}, majoring in {{ $study_program }}, has completed the requirement for the Internship Final Report submission and that the aforementioned student has passed the Internship Final Presentation.
    </div>

    <div class="signature-section">
        <div style="margin-bottom: 10px;">Approved by: {{ $mentor_name }}</div>
        
        @if($mentor_signature)
            <img src="{{ $mentor_signature }}" class="signature-image" alt="Signature">
        @else
            <div class="empty-signature"></div>
        @endif
        
        <div class="mentor-name">
            {{ $mentor_name }}
        </div>
    </div>
</body>
</html>
