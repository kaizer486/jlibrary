<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google Callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'user',
                    'wallet_balance' => 0,
                    'referral_code' => User::generateReferralCode(),
                ]);
            } else {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }
            
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Logged in successfully with Google!');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }

    // Redirect to GitHub
    public function redirectToGitHub()
    {
        return Socialite::driver('github')->redirect();
    }

    // Handle GitHub Callback
    public function handleGitHubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            
            // Check if user exists
            $user = User::where('email', $githubUser->getEmail())->first();
            
            if (!$user) {
                // Create new user
                $user = User::create([
                    'full_name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'email' => $githubUser->getEmail(),
                    'google_id' => null,
                    'password' => Hash::make(uniqid()),
                    'role' => 'user',
                    'wallet_balance' => 0,
                    'referral_code' => User::generateReferralCode(),
                ]);
            }
            
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Logged in successfully with GitHub!');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'GitHub login failed. Please try again.');
        }
    }
}