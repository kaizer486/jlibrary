@extends('layouts.admin')

@section('title', 'Instructor Dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <i class="ti ti-school text-purple-600 text-3xl"></i>
        <h1 class="text-3xl font-bold text-gray-900">Instructor Dashboard</h1>
    </div>
    <p class="text-gray-600">Welcome back, {{ Auth::user()->full_name }}!</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-5 border-l-4 border-blue-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Courses</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalCourses ?? 0 }}</p>
            </div>
            <i class="ti ti-video text-4xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border-l-4 border-green-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalStudents ?? 0 }}</p>
            </div>
            <i class="ti ti-users text-4xl text-green-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border-l-4 border-yellow-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Quizzes</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalQuizzes ?? 0 }}</p>
            </div>
            <i class="ti ti-brain text-4xl text-yellow-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border-l-4 border-purple-500 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Avg. Completion</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($avgCompletion ?? 0) }}%</p>
            </div>
            <i class="ti ti-chart-bar text-4xl text-purple-500"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('instructor.courses.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-plus text-2xl mb-1 block"></i>
        <span class="font-semibold">Create Course</span>
    </a>
    <a href="{{ route('instructor.courses.index') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-video text-2xl mb-1 block"></i>
        <span class="font-semibold">Manage Courses</span>
    </a>
    <a href="{{ route('quizzes.index') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-brain text-2xl mb-1 block"></i>
        <span class="font-semibold">Manage Quizzes</span>
    </a>
</div>

<!-- Recent Courses -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-900">📚 Recent Courses</h2>
        <a href="{{ route('instructor.courses.index') }}" class="text-sm text-purple-600">View All →</a>
    </div>
    <div class="space-y-3">
        @forelse(($recentCourses ?? collect()) as $course)
        <div class="flex items-center justify-between py-2 border-b">
            <div>
                <p class="font-medium text-gray-900">{{ $course->title }}</p>
                <p class="text-sm text-gray-500">{{ $course->enrollments_count ?? 0 }} students enrolled</p>
            </div>
            <div class="flex items-center gap-2">
                @if($course->status == 'draft')
                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">📝 Draft</span>
                @elseif($course->status == 'published')
                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Published</span>
                @elseif($course->status == 'archived')
                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">📦 Archived</span>
                @else
                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ ucfirst($course->status) }}</span>
                @endif
                <a href="{{ route('instructor.courses.edit', $course) }}" class="text-purple-600 hover:text-purple-700">Edit</a>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">No courses created yet. Click "Create Course" to get started.</p>
        @endforelse
    </div>
</div>
@endsection