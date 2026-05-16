<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Portfolio Evaluation - {{ $mahasiswa['nama_lengkap'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .page { padding: 30px 40px; }

        .header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 3px double #333; }
        .header h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 3px; }
        .header h2 { font-size: 12px; font-weight: normal; color: #555; font-style: italic; }

        .section-title { font-size: 12px; font-weight: bold; color: #333; padding-bottom: 4px; border-bottom: 1px solid #ddd; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-top: 18px; }

        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; vertical-align: top; font-size: 11px; }
        .info-table td.label { width: 130px; font-weight: bold; color: #555; }
        .info-table td.sep { width: 10px; }

        .identity-container { width: 100%; }
        .identity-container::after { content: ""; display: table; clear: both; }
        .identity-box { width: 48%; float: left; }
        .identity-box:last-child { float: right; }

        .rubric-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .rubric-table th { background-color: #e8e8e8; border: 1px solid #999; padding: 6px 10px; text-align: left; font-size: 10px; font-weight: bold; }
        .rubric-table th.center { text-align: center; }
        .rubric-table td { border: 1px solid #999; padding: 5px 10px; font-size: 10px; }
        .rubric-table td.center { text-align: center; }
        .rubric-table td.score { text-align: center; font-weight: bold; font-size: 11px; }
        .rubric-table tr.category-row { background-color: #f5f5f5; }
        .rubric-table tr.category-row td { font-weight: bold; font-size: 11px; }
        .rubric-table tr.total-row { background-color: #e0e0e0; }
        .rubric-table tr.total-row td { font-weight: bold; font-size: 12px; border-top: 2px solid #333; }

        .result-box { border: 2px solid #333; padding: 10px 15px; margin-bottom: 15px; text-align: center; }
        .result-box .result-label { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .result-box .result-value { font-size: 18px; font-weight: bold; margin-top: 3px; }
        .result-qualified { color: #16a34a; }
        .result-not-qualified { color: #dc2626; }

        .notes-box { border: 1px solid #ccc; border-radius: 4px; padding: 10px 12px; min-height: 50px; font-size: 10px; line-height: 1.6; background-color: #fafafa; margin-bottom: 15px; }

        .signature-section { width: 100%; margin-top: 30px; }
        .signature-box { width: 40%; float: right; text-align: center; }
        .signature-img { height: 60px; margin: 10px auto; display: block; }
        .signature-placeholder { height: 70px; margin: 10px 0; line-height: 70px; color: #999; font-style: italic; font-size: 10px; }
        .signature-line { width: 150px; border-bottom: 1px solid black; margin: 0 auto 8px auto; }
        .signature-name { font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .clear { clear: both; }

        .footer { margin-top: 35px; padding-top: 8px; border-top: 1px solid #ddd; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="page">

        <!-- Header -->
        <div class="header">
            <h1>Portfolio Evaluation</h1>
            <h2>Guidance for Supervisor / University Mentor</h2>
        </div>

        <!-- Evaluation Info -->
        <div class="identity-container">
            <div class="identity-box">
                <div class="section-title">Evaluation Information</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Date</td>
                        <td class="sep">:</td>
                        <td>{{ $evaluation['evaluation_date'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Company / Agency</td>
                        <td class="sep">:</td>
                        <td>{{ $evaluation['company_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Department</td>
                        <td class="sep">:</td>
                        <td>{{ $evaluation['department'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Position</td>
                        <td class="sep">:</td>
                        <td>{{ $evaluation['position'] }}</td>
                    </tr>
                </table>
            </div>
            <div class="identity-box">
                <div class="section-title">Intern Information</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Intern Name</td>
                        <td class="sep">:</td>
                        <td>{{ $mahasiswa['nama_lengkap'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIM</td>
                        <td class="sep">:</td>
                        <td>{{ $mahasiswa['nim'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program</td>
                        <td class="sep">:</td>
                        <td>{{ $mahasiswa['prodi'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">Period</td>
                        <td class="sep">:</td>
                        <td>{{ $tanggal_mulai }} — {{ $tanggal_selesai }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Rubric Table -->
        <div class="section-title">Portfolio Evaluation Rubric</div>
        <table class="rubric-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Criteria</th>
                    <th class="center" style="width: 10%;">Weight</th>
                    <th style="width: 30%;">Sub-Criteria</th>
                    <th style="width: 15%;">Rating</th>
                    <th class="center" style="width: 10%;">Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    @foreach($cat['items'] as $idx => $item)
                    <tr>
                        @if($idx === 0)
                        <td rowspan="{{ count($cat['items']) }}" style="vertical-align: middle; font-weight: bold;">
                            {{ $cat['label'] }}
                        </td>
                        <td rowspan="{{ count($cat['items']) }}" class="center" style="vertical-align: middle;">
                            {{ $cat['weight'] }}
                        </td>
                        @endif
                        <td>{{ $item['label'] ?? '—' }}</td>
                        <td>{{ $item['rating'] }}</td>
                        <td class="score">{{ $item['score'] }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 15px;">OVERALL SCORE</td>
                    <td class="score" style="font-size: 14px;">{{ number_format($evaluation['overall_score'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Qualification Result -->
        <div class="result-box">
            <div class="result-label">Qualification Result</div>
            <div class="result-value {{ $evaluation['qualification_result'] === 'Qualified' ? 'result-qualified' : 'result-not-qualified' }}">
                {{ $evaluation['qualification_result'] }}
            </div>
        </div>

        <!-- Comments -->
        <div class="section-title">Comments / Feedback</div>
        <div class="notes-box">
            {{ $evaluation['comments'] }}
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Date: {{ $evaluation['evaluation_date'] }}</p>
                <p style="margin-top: 5px;">{{ $evaluation['evaluator_type'] }},</p>

                @if($signatureBase64)
                    <img src="{{ $signatureBase64 }}" class="signature-img" alt="Signature">
                @else
                    <div class="signature-placeholder">(Signature not available)</div>
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $evaluator_name }}</div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This document was auto-generated by Magang Horizon System &bull; {{ now()->format('d F Y H:i') }}
        </div>

    </div>
</body>
</html>
