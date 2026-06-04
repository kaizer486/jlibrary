<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user has instructor role
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access. Instructor role required.');
        }
        
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        $totalCourses = Course::where('instructor_id', auth()->id())->count();
        $totalQuizzes = Quiz::where('instructor_id', auth()->id())->count();
        
        // Get total students enrolled in instructor's courses
        $courseIds = Course::where('instructor_id', auth()->id())->pluck('id');
        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'enrolled')
            ->distinct('user_id')
            ->count('user_id');
        
        // Average completion rate
        $avgCompletion = Enrollment::whereIn('course_id', $courseIds)
            ->avg('progress') ?? 0;
        
        // Recent courses with enrollments count
        $recentCourses = Course::where('instructor_id', auth()->id())
            ->withCount('enrollments')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('instructor.dashboard', compact(
            'institution', 'totalCourses', 'totalQuizzes', 
            'totalStudents', 'avgCompletion', 'recentCourses'
        ));
    }
}