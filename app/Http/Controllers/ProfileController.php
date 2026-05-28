<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);
        
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->bio = $request->bio;
        $user->location = $request->location;
        $user->occupation = $request->occupation;
        $user->birth_date = $request->birth_date;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->github_url = $request->github_url;
        $user->instagram_url = $request->instagram_url;
        $user->website_url = $request->website_url;
        
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return redirect()->back()->with('success', 'Avatar updated successfully!');
    }

    public function updateCover(Request $request)
    {
        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $user = Auth::user();

        if ($user->cover_photo && Storage::disk('public')->exists($user->cover_photo)) {
            Storage::disk('public')->delete($user->cover_photo);
        }

        $path = $request->file('cover_photo')->store('covers', 'public');
        $user->cover_photo = $path;
        $user->save();

        return redirect()->back()->with('success', 'Cover photo updated successfully!');
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->avatar = null;
        $user->save();
        
        return redirect()->back()->with('success', 'Avatar removed successfully!');
    }

    public function deleteCover()
    {
        $user = Auth::user();
        
        if ($user->cover_photo && Storage::disk('public')->exists($user->cover_photo)) {
            Storage::disk('public')->delete($user->cover_photo);
        }
        
        $user->cover_photo = null;
        $user->save();
        
        return redirect()->back()->with('success', 'Cover photo removed successfully!');
    }

    public function show($username)
    {
        $user = User::where('full_name', 'LIKE', "%{$username}%")
            ->orWhere('id', $username)
            ->firstOrFail();
        
        $books = $user->books()->wherePivot('status', 'completed')->latest()->take(6)->get();
        $certificates = $user->certificates()->latest()->take(3)->get();
        
        return view('profile.show', compact('user', 'books', 'certificates'));
    }
}