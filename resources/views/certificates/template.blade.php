<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion - JLIBRARY</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: white;
        }
        .certificate {
            width: 100%;
            height: 100%;
            border: 20px solid #7c3aed;
            padding: 30px;
            position: relative;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 36px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 18px;
            color: #666;
        }
        .content {
            text-align: center;
            margin: 40px 0;
        }
        .recipient {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            text-decoration: underline;
        }
        .description {
            font-size: 18px;
            color: #555;
            margin: 20px 0;
        }
        .book-title {
            font-size: 24px;
            font-weight: bold;
            color: #7c3aed;
            margin: 15px 0;
        }
        .book-author {
            font-size: 16px;
            color: #888;
        }
        .details {
            margin: 30px 0;
            padding: 20px;
            background: #f5f3ff;
            border-radius: 10px;
        }
        .score {
            font-size: 24px;
            font-weight: bold;
            color: #22c55e;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
        .cert-number {
            font-family: monospace;
            font-size: 11px;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="title">JLIBRARY</div>
            <div class="subtitle">Certificate of Completion</div>
        </div>
        
        <div class="content">
            <p>This certificate is proudly presented to</p>
            <div class="recipient">{{ $user_name }}</div>
            
            <div class="description">
                For successfully completing the quiz for
            </div>
            
            <div class="book-title">{{ $book_title }}</div>
            <div class="book-author">by {{ $book_author }}</div>
            
            <div class="details">
                <div class="score">{{ $percentage }}%</div>
                <div>Score: {{ $score }}/{{ $total }}</div>
            </div>
            
            <div class="signature">
                <p>_____________________</p>
                <p>Josiah Nashon</p>
                <p>Project Manager, JLIBRARY</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Issued on: {{ $date }}</p>
            <p class="cert-number">Certificate Number: {{ $certificate_number }}</p>
            <p>© {{ date('Y') }} JLIBRARY - Learn. Share. Grow Together.</p>
        </div>
    </div>
</body>
</html>