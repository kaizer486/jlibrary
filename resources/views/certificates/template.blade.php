<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>JLIBRARY Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate {
            width: 100%;
            height: 100vh;
            padding: 40px 60px;
            border: 8px double #7c3aed;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 2px solid #d8b4fe;
            pointer-events: none;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        .logo img {
            height: 50px;
            width: auto;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #6d28d9;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            font-style: italic;
            margin: 2px 0;
        }
        .subtitle-sm {
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            margin-bottom: 20px;
        }
        hr {
            border: none;
            border-top: 1px solid #d8b4fe;
            margin: 10px 0;
        }
        .title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #6d28d9;
            font-family: 'Georgia', serif;
            margin: 15px 0;
            letter-spacing: 2px;
        }
        .body-text {
            text-align: center;
            color: #4b5563;
            font-size: 18px;
            margin: 5px 0;
        }
        .user-name {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            color: #6d28d9;
            margin: 10px 0;
        }
        .book-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #7c3aed;
            margin: 8px 0;
        }
        .book-author {
            text-align: center;
            color: #4b5563;
            font-size: 18px;
        }
        .score-box {
            display: inline-block;
            background: #f5f3ff;
            padding: 10px 30px;
            border-radius: 8px;
            border: 1px solid #d8b4fe;
            margin: 15px auto;
            text-align: center;
        }
        .score-label {
            color: #6b7280;
            font-size: 13px;
        }
        .score-value {
            font-size: 24px;
            font-weight: 700;
            color: #6d28d9;
        }
        .score-value span {
            font-size: 16px;
            font-weight: 400;
            color: #6b7280;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #d8b4fe;
        }
        .footer-left p {
            margin: 2px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .footer-left .label {
            font-weight: 600;
            color: #374151;
        }
        .signature {
            text-align: right;
        }
        .signature-line {
            width: 120px;
            border-bottom: 2px solid #7c3aed;
            margin-left: auto;
        }
        .signature-label {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ public_path('images/jlibrary.jpeg') }}" alt="JLIBRARY">
            <span class="logo-text">JLIBRARY</span>
        </div>
        <p class="subtitle">Learn. Share. Grow Together.</p>
        <p class="subtitle-sm">Education Empowers All.</p>
        
        <hr>
        
        <!-- Title -->
        <h1 class="title">JLIBRARY LEARNING CERTIFICATE</h1>
        
        <!-- Body -->
        <p class="body-text">This is to certify that</p>
        <h2 class="user-name">{{ $user_name }}</h2>
        
        <p class="body-text">has successfully completed reading</p>
        <h3 class="book-title">"{{ $book_title }}"</h3>
        <p class="book-author">written by {{ $book_author }}</p>
        
        <p class="body-text">through JLIBRARY and has met all requirements for certification.</p>
        
        <!-- Score -->
        <div style="text-align: center;">
            <div class="score-box">
                <p class="score-label">Quiz Score</p>
                <p class="score-value">{{ $score }}/{{ $total }} <span>({{ $percentage }}%)</span></p>
            </div>
        </div>
        
        <hr>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <p><span class="label">Date</span><br>{{ $date }}</p>
            </div>
            <div class="footer-left">
                <p><span class="label">Certificate ID</span><br>{{ $certificate_number }}</p>
            </div>
            <div class="signature">
                <p style="font-size:13px; font-weight:600; color:#374151; margin:0;">JLIBRARY Administration</p>
                <div class="signature-line"></div>
                <p class="signature-label">Authorized Signature</p>
            </div>
        </div>
    </div>
</body>
</html>