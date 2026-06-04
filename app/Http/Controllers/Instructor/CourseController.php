<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        // Check if user has instructor role
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        $courses = Course::where('instructor_id', auth()->id())
            ->withCount(['lessons', 'enrollments'])
            ->latest()
            ->paginate(10);
            
        return view('instructor.courses.index', compact('courses'));
    }
    
    public function create()
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('instructor.courses.create');
    }
    
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'duration' => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('course-covers', 'public');
        }
        
        $course = Course::create([
            'instructor_id' => auth()->id(),
            'institution_id' => auth()->user()->institution_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'description' => $request->description,
            'level' => $request->level,
            'status' => 'draft',
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'duration' => $request->duration,
            'cover_image' => $coverPath,
        ]);
        
        return redirect()->route('instructor.courses.edit', $course)
            ->with('success', 'Course created! Now add lessons.');
    }
    
    public function edit(Course $course)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $lessons = $course->lessons;
        return view('instructor.courses.edit', compact('course', 'lessons'));
    }
    
    public function update(Request $request, Course $course)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:draft,published,archived',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'duration' => 'nullable|integer|min:0',
        ]);
        
        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'level' => $request->level,
            'status' => $request->status,
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'duration' => $request->duration,
        ]);
        
        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $course->cover_image = $request->file('cover_image')->store('course-covers', 'public');
            $course->save();
        }
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course updated successfully!');
    }
    
    public function destroy(Course $course)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }
        
        // Delete lessons first
        foreach ($course->lessons as $lesson) {
            $lesson->delete();
        }
        
        $course->delete();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course deleted.');
    }
    
    public function addLesson(Request $request, Course $course)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer',
        ]);
        
        $order = $course->lessons()->max('order') + 1;
        
        Lesson::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'order' => $order,
        ]);
        
        return redirect()->back()->with('success', 'Lesson added!');
    }
    
    public function updateLesson(Request $request, Lesson $lesson)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($lesson->course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer',
            'order' => 'nullable|integer',
        ]);
        
        $lesson->update($request->all());
        
        return redirect()->back()->with('success', 'Lesson updated!');
    }
    
    public function deleteLesson(Lesson $lesson)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($lesson->course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $lesson->delete();
        
        return redirect()->back()->with('success', 'Lesson deleted.');
    }
    
    public function enrollments(Course $course)
    {
        if (!auth()->user()->hasRole('instructor')) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        
        $enrollments = $course->enrollments()->with('user')->latest()->paginate(20);
        
        return view('instructor.courses.enrollments', compact('course', 'enrollments'));
    }
}