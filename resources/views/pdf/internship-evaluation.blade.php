<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internship Evaluation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }

        .page { padding: 20px 30px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1a365d; padding-bottom: 10px; }
        .header h1 { font-size: 16px; font-weight: bold; color: #1a365d; margin-bottom: 2px; }
        .header h2 { font-size: 11px; color: #4a5568; font-weight: normal; font-style: italic; }

        /* Info Table */
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 3px 8px; vertical-align: top; }
        .info-label { font-weight: bold; color: #4a5568; width: 110px; }
        .info-value { color: #1a202c; }

        /* Rubric Table */
        .rubric-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .rubric-table th, .rubric-table td { border: 1px solid #cbd5e0; padding: 6px 8px; text-align: center; vertical-align: middle; }
        .rubric-table thead th { background: #1a365d; color: #fff; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        .rubric-table tbody td { font-size: 9px; }
        .rubric-table .cat-label { text-align: left; font-weight: bold; background: #f7fafc; }
        .rubric-table .selected { background: #ebf8ff; font-weight: bold; color: #2b6cb0; border: 2px solid #3182ce; }
        .rubric-table .score-cell { font-weight: bold; font-size: 11px; }

        /* Result Section */
        .result-section { margin-bottom: 15px; }
        .result-row { width: 100%; border-collapse: collapse; }
        .result-row td { padding: 8px 12px; vertical-align: middle; }
        .result-box { border: 1px solid #cbd5e0; text-align: center; }
        .score-big { font-size: 20px; font-weight: bold; }
        .pass { color: #276749; }
        .fail { color: #c53030; }
        .pass-bg { background: #f0fff4; }
        .fail-bg { background: #fff5f5; }

        /* Comments */
        .comments-section { border: 1px solid #cbd5e0; padding: 10px 12px; margin-bottom: 15px; }
        .comments-label { font-weight: bold; color: #4a5568; font-size: 9px; text-transform: uppercase; margin-bottom: 4px; }
        .comments-text { color: #1a202c; line-height: 1.5; }

        /* Pass Criteria Note */
        .note { font-size: 8px; color: #718096; font-style: italic; margin-bottom: 15px; padding: 6px 10px; background: #f7fafc; border-left: 3px solid #a0aec0; }

        /* Signature */
        .signature-section { margin-top: 20px; }
        .signature-table { width: 100%; }
        .signature-table td { vertical-align: bottom; }
        .signature-box { text-align: center; width: 250px; }
        .signature-box .sig-label { font-size: 9px; color: #4a5568; margin-bottom: 5px; }
        .signature-box .sig-image { height: 60px; margin-bottom: 5px; }
        .signature-box .sig-line { border-top: 1px solid #333; padding-top: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>INTERNSHIP EVALUATION</h1>
            <h2>Guidance for Supervisor / University Mentor</h2>
        </div>

        <!-- Info -->
        <table class="info-table">
            <tr>
                <td class="info-label">Date:</td>
                <td class="info-value">{{ $evaluation['evaluation_date'] }}</td>
                <td class="info-label">Company / Agency:</td>
                <td class="info-value">{{ $evaluation['company_name'] }}</td>
            </tr>
            <tr>
                <td class="info-label">Intern:</td>
                <td class="info-value">{{ $mahasiswa['nama_lengkap'] }} ({{ $mahasiswa['nim'] }})</td>
                <td class="info-label">Supervisor:</td>
                <td class="info-value">{{ $evaluator_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Department:</td>
                <td class="info-value">{{ $evaluation['department'] }}</td>
                <td class="info-label">Position:</td>
                <td class="info-value">{{ $evaluation['position'] }}</td>
            </tr>
            <tr>
                <td class="info-label">Internship Period:</td>
                <td class="info-value" colspan="3">{{ $tanggal_mulai }} — {{ $tanggal_selesai }}</td>
            </tr>
        </table>

        <!-- Rubric Table -->
        <table class="rubric-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Internship</th>
                    <th style="width: 17%;">Exceptional</th>
                    <th style="width: 17%;">Exceeds Expectations</th>
                    <th style="width: 17%;">Meets Expectations</th>
                    <th style="width: 17%;">Nears Expectations</th>
                    <th style="width: 17%;">Below Expectations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td class="cat-label">{{ $cat['label'] }} ({{ $cat['weight'] }})</td>
                    @php
                        $ratings = ['exceptional', 'exceeds', 'meets', 'nears', 'below'];
                    @endphp
                    @foreach($ratings as $rating)
                        <td class="{{ $cat['rating'] === $rating ? 'selected' : '' }}">
                            @if($cat['rating'] === $rating)
                                <strong>✓ {{ number_format($cat['score'], 1) }}</strong>
                            @else
                                {{ $cat['descriptions'][$rating] ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Result -->
        <div class="result-section">
            <table class="result-row" style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <div class="result-box" style="padding: 10px;">
                            <div style="font-size: 9px; color: #4a5568; text-transform: uppercase; margin-bottom: 4px;">Overall Score</div>
                            <div class="score-big {{ (float)$evaluation['overall_score'] >= 50 ? 'pass' : 'fail' }}">
                                {{ number_format((float)$evaluation['overall_score'], 2) }} / 100
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%;">
                        <div class="result-box {{ $evaluation['pass_status'] === 'PASS' ? 'pass-bg' : 'fail-bg' }}" style="padding: 10px;">
                            <div style="font-size: 9px; color: #4a5568; text-transform: uppercase; margin-bottom: 4px;">Result</div>
                            <div class="score-big {{ $evaluation['pass_status'] === 'PASS' ? 'pass' : 'fail' }}">
                                {{ $evaluation['pass_status'] }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Pass Criteria Note -->
        <div class="note">
            *An intern PASSES internship if the overall score is at least 50 AND no category is rated "Below Expectations."<br>
            *An intern FAILS internship if any category is rated "Below Expectations" regardless of overall score.
        </div>

        <!-- Comments -->
        @if($evaluation['comments'] !== '-' || $evaluation['feedback'] !== '-')
        <div class="comments-section">
            @if($evaluation['comments'] !== '-')
                <div class="comments-label">Comments</div>
                <div class="comments-text">{{ $evaluation['comments'] }}</div>
            @endif
            @if($evaluation['feedback'] !== '-')
                <div class="comments-label" style="margin-top: 8px;">Additional Feedback</div>
                <div class="comments-text">{{ $evaluation['feedback'] }}</div>
            @endif
        </div>
        @endif

        <!-- Signature -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">
                        <div class="signature-box" style="display: inline-block;">
                            <div class="sig-label">Supervisor / University Mentor</div>
                            @if($signatureBase64)
                                <img src="{{ $signatureBase64 }}" class="sig-image" alt="Signature" />
                            @else
                                <div style="height: 60px;"></div>
                            @endif
                            <div class="sig-line">{{ $evaluator_name }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
