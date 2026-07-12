<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JLIBRARY - Password Reset</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e9e8e6;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
            overflow: hidden;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .logo-text {
            color: white;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
        }
        h1 {
            color: #1e293b;
            font-size: 32px;
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 16px;
            margin-top: 4px;
            font-weight: 400;
        }
        .content {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 30px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        h2 {
            color: #1e293b;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }
        p {
            color: #334155;
            line-height: 1.7;
            margin-bottom: 16px;
            font-size: 15px;
        }
        .greeting {
            color: #f59e0b;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            border: none;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(245, 158, 11, 0.4);
        }
        .button-container {
            text-align: center;
        }
        .divider {
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            margin: 25px 0;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 30px;
        }
        .footer a {
            color: #f59e0b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: #f97316;
            text-decoration: underline;
        }
        .expiry {
            background: rgba(245, 158, 11, 0.08);
            border-left: 3px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .expiry p {
            color: #475569;
            margin: 0;
            font-size: 14px;
        }
        .expiry strong {
            color: #f59e0b;
        }
        .link-fallback {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            word-break: break-all;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }
        .link-fallback p {
            color: #64748b;
            font-size: 13px;
            margin: 5px 0;
        }
        .link-fallback a {
            color: #f59e0b;
            text-decoration: none;
            font-size: 13px;
        }
        .link-fallback a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .card {
                padding: 24px;
            }
            .content {
                padding: 20px;
            }
            h1 {
                font-size: 24px;
            }
            h2 {
                font-size: 18px;
            }
            .button {
                padding: 12px 30px;
                font-size: 14px;
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
            .container {
                padding: 20px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo-container">
               <div class="logo" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); width: 80px; height: 80px; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3); overflow: hidden;">
    @if(!empty($logoBase64))
        <img src="{{ $logoBase64 }}" alt="JLIBRARY" style="width: 100%; height: 100%; object-fit: cover;">
    @else
        <span style="color: white; font-size: 28px; font-weight: 700; letter-spacing: -1px;">JL</span>
    @endif
</div>
                <h1>JLIBRARY</h1>
                <div class="subtitle">Reset Your Password</div>
            </div>

            <div class="content">
                <h2>🔐 Password Reset Request</h2>
                
                <p>Hello <span class="greeting">{{ $greeting ?? 'User' }}</span>!</p>
                
                <p>You are receiving this email because we received a password reset request for your account.</p>

                <div class="button-container">
                    <a href="{{ $actionUrl }}" class="button">🔄 Reset Password</a>
                </div>

                <div class="expiry">
                    <p>⏰ This password reset link will expire in <strong>{{ config('auth.passwords.users.expire') }} minutes</strong>.</p>
                </div>

                <p style="font-size: 14px; color: #64748b;">If you did not request a password reset, no further action is required.</p>

                <div class="divider"></div>

                <div class="link-fallback">
                    <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below:</p>
                    <p><a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
                </div>
            </div>

            <div class="footer">
                <p style="color: #1e293b; font-weight: 500; margin-bottom: 8px;">© {{ date('Y') }} JLIBRARY. All rights reserved.</p>
                <p style="color: #94a3b8; font-size: 11px;">
                    <a href="{{ $appUrl ?? config('app.url') }}" style="color: #f59e0b;">Website</a> • 
                    <a href="{{ $appUrl ?? config('app.url') }}/support" style="color: #f59e0b;">Support</a> • 
                    <a href="{{ $appUrl ?? config('app.url') }}/privacy" style="color: #f59e0b;">Privacy Policy</a>
                </p>
                <p style="margin-top: 10px; font-size: 11px; color: #94a3b8;">
                    This email was sent to you because you requested a password reset.
                </p>
            </div>
        </div>
    </div>
</body>
</html>