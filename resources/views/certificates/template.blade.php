<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>JLIBRARY Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: a4 landscape;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: "Georgia", "Times New Roman", serif;
            background: white;
        }
        .certificate-wrapper {
            position: relative;
            width: 842pt;
            height: 595pt;
        }
        .certificate-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 842pt;
            height: 595pt;
            object-fit: cover;
        }
        .certificate-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 10;
            padding: 40px 60px;
        }
        /* User Name - Large, centered */
        .user-name {
            font-size: 52px;
            font-weight: 700;
            color: #1a202c;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 30px;
        }
        /* Book Title */
        .book-title {
            font-size: 36px;
            font-weight: 600;
            color: #2d3748;
            margin-top: 10px;
        }
        /* Book Author */
        .book-author {
            font-size: 24px;
            font-weight: 400;
            color: #4a5568;
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <img src="{{ $background_image }}" class="certificate-bg" alt="Certificate Background">
        <div class="certificate-content">
            <!-- Only these 3 things overlay on the image -->
            <div class="user-name">{{ $user_name }}</div>
            <div class="book-title">"{{ $book_title }}"</div>
            <div class="book-author">by {{ $book_author }}</div>
        </div>
    </div>
</body>
</html>