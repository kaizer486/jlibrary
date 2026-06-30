<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $institution?->name ?? 'JLIBRARY' }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6d28d9, #db2777);
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            color: #4a5568;
            line-height: 1.6;
            margin: 16px 0;
        }
        .credentials {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #7c3aed;
        }
        .credentials p {
            margin: 8px 0;
        }
        .credentials .label {
            font-weight: 600;
            color: #2d3748;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #7c3aed, #db2777);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 16px 0;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 14px;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e9d8fd;
            color: #44337a;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Welcome to {{ $institution?->name ?? 'JLIBRARY' }}</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $user->full_name }}</strong>,</p>
            
            <p>You have been added as a member of <strong>{{ $institution?->name ?? 'our institution' }}</strong> on the JLIBRARY platform.</p>
            
            <div class="credentials">
                <p><span class="label">📧 Email:</span> {{ $user->email }}</p>
                <p><span class="label">🔑 Temporary Password:</span> <code style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-weight: 600;">{{ $password }}</code></p>
                <p><span class="label">🎯 Role:</span> <span class="role-badge">{{ $user->getRoleLabel() }}</span></p>
            </div>
            
            <p>You can log in using the credentials above. For security reasons, we strongly recommend changing your password after your first login.</p>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">🔐 Login to Your Account</a>
            </div>
            
            <p style="font-size: 14px; color: #718096; text-align: center;">
                Once logged in, you can access your dashboard, browse books, connect with the community, and much more.
            </p>
            
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
            
            <p style="font-size: 14px; color: #718096;">
                <strong>Need help?</strong> If you have any questions, please contact your institution administrator.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} JLIBRARY. All rights reserved.</p>
            <p style="font-size: 12px;">This email was sent to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>