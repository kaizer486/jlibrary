<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        $stats = [
            'total_members' => $institution->users()->count(),
            'total_books' => $institution->books()->count(),
            'total_admins' => $institution->users()->where('is_institution_admin', true)->count(),
            'total_librarians' => $institution->users()->where('role', 'librarian')->count(),
        ];
        
        $recentMembers = $institution->users()->latest()->limit(5)->get();
        $recentBooks = $institution->books()->latest()->limit(5)->get();
        
        return view('admin.institutions.dashboard', compact('institution', 'stats', 'recentMembers', 'recentBooks'));
    }
}