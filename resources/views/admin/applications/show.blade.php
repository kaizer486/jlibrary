@extends('layouts.admin')

@section('title', 'Review Application')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.applications.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Applications
        </a>
    </div>
    
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Applicant Info -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3">
                <h2 class="text-white font-semibold">👤 Applicant Information</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Name:</span>
                    <span class="font-medium">{{ $application->user->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Email:</span>
                    <span class="font-medium">{{ $application->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Phone:</span>
                    <span class="font-medium">{{ $application->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Applied for:</span>
                    <span>{!! $application->type_label !!}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Submitted:</span>
                    <span>{{ $application->created_at->format('F d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span>{!! $application->status_badge !!}</span>
                </div>
            </div>
        </div>
        
        <!-- Business Info (for bookseller) -->
        @if($application->type === 'bookseller')
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-3">
                <h2 class="text-white font-semibold">🏢 Business Information</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Business Name:</span>
                    <span class="font-medium">{{ $application->business_name ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">TIN Number:</span>
                    <span class="font-medium">{{ $application->tax_id ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Business Address:</span>
                    <p class="font-medium mt-1">{{ $application->business_address ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Application Message -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-3">
                <h2 class="text-white font-semibold">💬 Application Message</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700">{{ $application->message ?? 'No message provided.' }}</p>
            </div>
        </div>
        
        <!-- Documents -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-3">
                <h2 class="text-white font-semibold">📎 Submitted Documents</h2>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.applications.download', [$application, 'id_document']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <i class="ti ti-id text-2xl text-blue-600"></i>
                        <div>
                            <p class="font-medium">National ID / Passport</p>
                            <p class="text-xs text-gray-500">Click to download</p>
                        </div>
                    </a>
                    
                    @if($application->certificate_document)
                    <a href="{{ route('admin.applications.download', [$application, 'certificate_document']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <i class="ti ti-certificate text-2xl text-green-600"></i>
                        <div>
                            <p class="font-medium">Education Certificate</p>
                            <p class="text-xs text-gray-500">Click to download</p>
                        </div>
                    </a>
                    @endif
                    
                    @if($application->business_license)
                    <a href="{{ route('admin.applications.download', [$application, 'business_license']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <i class="ti ti-license text-2xl text-purple-600"></i>
                        <div>
                            <p class="font-medium">Business License</p>
                            <p class="text-xs text-gray-500">Click to download</p>
                        </div>
                    </a>
                    @endif
                    
                    @if($application->tax_certificate)
                    <a href="{{ route('admin.applications.download', [$application, 'tax_certificate']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <i class="ti ti-file-text text-2xl text-red-600"></i>
                        <div>
                            <p class="font-medium">Tax Certificate</p>
                            <p class="text-xs text-gray-500">Click to download</p>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Admin Actions -->
        @if($application->status === 'pending')
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-3">
                <h2 class="text-white font-semibold">⚡ Admin Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                            ✅ Approve Application
                        </button>
                    </form>
                    
                    <!-- Reject Form -->
                    <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                        @csrf
                        <textarea name="admin_notes" rows="3" class="w-full px-4 py-2 border rounded-lg mb-2" placeholder="Reason for rejection... (required)"></textarea>
                        <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                            ❌ Reject Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Admin Notes (if rejected) -->
        @if($application->status === 'rejected' && $application->admin_notes)
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-3">
                <h2 class="text-white font-semibold">📝 Admin Notes</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700">{{ $application->admin_notes }}</p>
                <p class="text-xs text-gray-400 mt-2">Reviewed by: {{ $application->reviewer->full_name ?? 'Unknown' }} on {{ $application->reviewed_at->format('F d, Y') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection