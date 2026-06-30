@extends('layouts.admin')

@section('title', 'Review Application')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.applications.index') }}" class="text-purple-600 hover:text-purple-800">
            <i class="ti ti-arrow-left"></i> Back to Applications
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">Review Application</h1>
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                    @if($application->type === 'author') 📚 Author
                    @elseif($application->type === 'bookseller') 📖 Bookseller
                    @elseif($application->type === 'publisher') 📰 Publisher
                    @elseif($application->type === 'researcher') 🔬 Researcher
                    @else {{ ucfirst($application->type) }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Applicant Info -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b pb-2">Applicant Information</h3>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="ti ti-user text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $application->user->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Submitted</p>
                        <p class="text-gray-800">{{ $application->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @if($application->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($application->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Approved</span>
                        @elseif($application->status === 'rejected')
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </div>

                    @if($application->approved_at)
                    <div>
                        <p class="text-sm text-gray-500">Reviewed By</p>
                        <p class="text-gray-800">{{ $application->approvedBy->full_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $application->approved_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Application Details -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b pb-2">Application Details</h3>
                    
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="text-gray-800 text-lg">
                            @if($application->type === 'author') 📚 Author
                            @elseif($application->type === 'bookseller') 📖 Bookseller
                            @elseif($application->type === 'publisher') 📰 Publisher
                            @elseif($application->type === 'researcher') 🔬 Researcher
                            @else {{ ucfirst($application->type) }}
                            @endif
                        </p>
                    </div>

                    @if($application->message)
                    <div>
                        <p class="text-sm text-gray-500">Message</p>
                        <div class="bg-gray-50 rounded-lg p-3 mt-1">
                            <p class="text-gray-700">{{ $application->message }}</p>
                        </div>
                    </div>
                    @endif

                    @if($application->admin_notes)
                    <div>
                        <p class="text-sm text-gray-500">Admin Notes</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-1">
                            <p class="text-red-700">{{ $application->admin_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($application->status === 'pending')
            <div class="mt-8 pt-6 border-t">
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition font-semibold flex items-center gap-2">
                            <i class="ti ti-check"></i> Approve Application
                        </button>
                    </form>
                    
                    <button onclick="document.getElementById('rejectForm').classList.toggle('hidden')" 
                            class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-semibold flex items-center gap-2">
                        <i class="ti ti-x"></i> Reject Application
                    </button>
                    
                    <a href="{{ route('admin.applications.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        Cancel
                    </a>
                </div>

                <!-- Reject Form -->
                <form id="rejectForm" method="POST" action="{{ route('admin.applications.reject', $application) }}" class="hidden mt-4 p-4 border rounded-lg bg-gray-50">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Reason for Rejection</label>
                        <textarea name="admin_notes" rows="3" required 
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500"
                                  placeholder="Please explain why this application is being rejected..."></textarea>
                    </div>
                    <div class="mt-3 flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                            Submit Rejection
                        </button>
                        <button type="button" onclick="document.getElementById('rejectForm').classList.add('hidden')" 
                                class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="mt-8 pt-6 border-t">
                <p class="text-gray-500 text-sm">This application has been reviewed.</p>
                <a href="{{ route('admin.applications.index') }}" class="mt-2 inline-block text-purple-600 hover:underline">
                    Back to Applications
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection