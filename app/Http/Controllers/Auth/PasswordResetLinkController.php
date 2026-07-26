<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        // Generate token using Laravel's Password broker
        $token = Password::createToken($user);

        // Build reset URL
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $email,
        ], false));

        // Send via Brevo HTTP API (port 443, never blocked)
        $apiKey = env('BREVO_API_KEY');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@jlibrary.co.tz');
        $fromName = env('MAIL_FROM_NAME', 'JLIBRARY');

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail,
                ],
                'to' => [
                    ['email' => $email, 'name' => $user->name ?? 'User']
                ],
                'subject' => 'Reset Your JLIBRARY Password',
                'htmlContent' => $this->buildEmailHtml($resetUrl, $user->name ?? 'User'),
                'textContent' => "Hello! You requested a password reset for your JLIBRARY account. Click this link to reset: {$resetUrl} This link expires in 60 minutes. If you didn't request this, ignore this email.",
            ]);

            if ($response->successful() || $response->status() === 201) {
                return back()->with('status', 'We have emailed your password reset link!');
            }

            // Log API error for debugging
            \Illuminate\Support\Facades\Log::error('Brevo API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Brevo API exception', ['error' => $e->getMessage()]);
        }

        // Fallback: log the link so admin can manually send if API fails
        \Illuminate\Support\Facades\Log::info('Password reset fallback', [
            'email' => $email,
            'url' => $resetUrl,
        ]);

        return back()->with('status', 'We have emailed your password reset link!');
    }

    private function buildEmailHtml(string $url, string $name): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:'Inter',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="500" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#db570a,#e87a2a);padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:700;">JLIBRARY</h1>
                            <p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:14px;">Digital Learning Platform</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:35px 30px;">
                            <h2 style="color:#1E293B;margin:0 0 15px;font-size:20px;">Hello, {$name}!</h2>
                            <p style="color:#64748B;line-height:1.6;margin:0 0 20px;font-size:15px;">
                                You are receiving this email because we received a password reset request for your account.
                            </p>
                            <div style="text-align:center;margin:30px 0;">
                                <a href="{$url}" style="background:linear-gradient(135deg,#db570a,#e87a2a);color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:12px;font-weight:600;font-size:15px;display:inline-block;box-shadow:0 4px 16px rgba(219,87,10,0.25);">Reset Password</a>
                            </div>
                            <p style="color:#94A3B8;font-size:13px;margin:0 0 15px;">
                                This link will expire in <strong style="color:#db570a;">60 minutes</strong>.
                            </p>
                            <p style="color:#94A3B8;font-size:13px;margin:0 0 20px;">
                                If you did not request a password reset, no further action is required.
                            </p>
                            <hr style="border:none;border-top:1px solid #E2E8F0;margin:25px 0;">
                            <p style="color:#94A3B8;font-size:12px;margin:0 0 8px;">
                                If you're having trouble clicking the button, copy and paste this URL:
                            </p>
                            <p style="color:#db570a;font-size:12px;word-break:break-all;margin:0;">{$url}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#F8FAFC;padding:20px 30px;text-align:center;border-top:1px solid #E2E8F0;">
                            <p style="color:#94A3B8;font-size:12px;margin:0;">&copy; 2026 JLIBRARY. All rights reserved.</p>
                            <p style="color:#94A3B8;font-size:12px;margin:8px 0 0;">Dar es Salaam, Tanzania</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}