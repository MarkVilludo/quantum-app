<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate — {{ $studentName }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: Georgia, "Times New Roman", serif;
            background: #0b1020;
            color: #f8fafc;
        }
        .cert {
            max-width: 820px;
            margin: 0 auto;
            padding: 48px 40px;
            border: 3px solid #22d3ee;
            border-radius: 16px;
            background: linear-gradient(145deg, #141b2d 0%, #1e293b 55%, #121528 100%);
            box-shadow: 0 0 40px rgba(34, 211, 238, 0.15);
            text-align: center;
        }
        .logo { display: block; margin: 0 auto 12px; width: 56px; height: 56px; }
        .brand { letter-spacing: 0.35em; font-size: 12px; color: #94a3b8; text-transform: uppercase; }
        h1 {
            margin: 16px 0 8px;
            font-size: 32px;
            font-weight: normal;
            color: #22d3ee;
        }
        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 32px; }
        .presented { font-size: 14px; color: #cbd5e1; margin-bottom: 8px; }
        .name {
            font-size: 36px;
            font-weight: bold;
            margin: 8px 0 24px;
            color: #f8fafc;
            border-bottom: 1px solid #334155;
            display: inline-block;
            padding-bottom: 8px;
            min-width: 60%;
        }
        .body { font-size: 16px; line-height: 1.6; color: #e2e8f0; max-width: 620px; margin: 0 auto 32px; }
        .meta { font-size: 13px; color: #64748b; margin-top: 24px; }
        .code { font-family: ui-monospace, monospace; color: #6366f1; letter-spacing: 0.08em; }
        .print-btn {
            display: block;
            margin: 24px auto 0;
            max-width: 820px;
            padding: 12px 20px;
            font-size: 15px;
            font-family: system-ui, sans-serif;
            border: none;
            border-radius: 8px;
            background: #22d3ee;
            color: #0b1020;
            cursor: pointer;
        }
        @media print {
            .print-btn { display: none; }
            body { background: #fff; color: #111; padding: 0; }
            .cert { box-shadow: none; border-color: #333; background: #fff; color: #111; }
            h1 { color: #333; }
            .name { color: #000; border-color: #ccc; }
            .body, .presented, .subtitle { color: #333; }
            .meta { color: #555; }
        }
    </style>
</head>
<body>
    <button type="button" class="print-btn" onclick="window.print()">Print or save as PDF</button>
    <div class="cert">
        <svg class="logo" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <ellipse cx="24" cy="24" rx="20" ry="7" fill="none" stroke="#22D3EE" stroke-width="2" transform="rotate(-25 24 24)"/>
            <circle cx="24" cy="26" r="9" fill="#6366F1"/>
            <circle cx="21" cy="23" r="2.5" fill="#818CF8" opacity="0.55"/>
            <circle cx="10" cy="12" r="1.2" fill="#FBBF24"/>
            <circle cx="38" cy="14" r="1" fill="#FBBF24"/>
        </svg>
        <div class="brand">Quantum Realm</div>
        <h1>Certificate of Completion</h1>
        <p class="subtitle">{{ $programTitle }}</p>
        <p class="presented">This certifies that</p>
        <p class="name">{{ $studentName }}</p>
        <p class="body">
            has successfully completed all year-level subjects and passed every final subject quiz
            across the full Learning Galaxy curriculum (1st through 4th Year).
        </p>
        <p class="meta">
            Date: {{ $graduatedAt->format('F j, Y') }}<br>
            Certificate ID: <span class="code">{{ $certificateCode }}</span>
        </p>
    </div>
</body>
</html>
