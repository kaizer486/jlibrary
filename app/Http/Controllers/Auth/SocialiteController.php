<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to the provider's OAuth page
     */
    public function redirect($provider)
    {
        $validProviders = ['google', 'github', 'facebook', 'twitter'];
        
        if (!in_array($provider, $validProviders)) {
            return redirect()->route('login')->withErrors(['provider' => 'Invalid provider']);
        }
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the provider's OAuth callback
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user exists
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // If user exists, update social login info
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }
            
            // Log the user in
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Socialite login error: ' . $e->getMessage());
            
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to login with ' . ucfirst($provider) . '. Please try again or use email login.'
            ]);
        }
    }
}