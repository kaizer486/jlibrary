@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.applications.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Applications
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Applicant Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-user"></i> Applicant Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Full Name</p>
                        <p class="font-semibold text-gray-800">{{ $application->user->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Email</p>
                        <p class="text-gray-700">{{ $application->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Phone</p>
                        <p class="text-gray-700">{{ $application->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Applied For</p>
                        <p class="capitalize text-gray-700">{{ $application->type }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Submitted</p>
                        <p class="text-gray-700">{{ $application->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Status</p>
                        @if($application->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @elseif($application->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Business Info (for booksellers) -->
            @if($application->type === 'bookseller' && ($application->business_name || $application->tax_id))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-building-store"></i> Business Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($application->business_name)
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Business Name</p>
                        <p class="font-semibold text-gray-800">{{ $application->business_name }}</p>
                    </div>
                    @endif
                    @if($application->tax_id)
                    <div>
                        <p class="text-xs text-gray-400 uppercase">TIN Number</p>
                        <p class="text-gray-700">{{ $application->tax_id }}</p>
                    </div>
                    @endif
                    @if($application->business_address)
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Business Address</p>
                        <p class="text-gray-700">{{ $application->business_address }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Application Details & Documents -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Application Message -->
            @if($application->message)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-message"></i> Application Message
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $application->message }}</p>
                </div>
            </div>
            @endif

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-file"></i> Submitted Documents
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-4">
                        @if($application->id_document)
                        <a href="{{ route('admin.applications.download', [$application, 'id_document']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                            <i class="ti ti-id text-2xl text-blue-600"></i>
                            <div>
                                <p class="font-medium text-gray-800">National ID / Passport</p>
                                <p class="text-xs text-gray-500">Click to download</p>
                            </div>
                        </a>
                        @endif
                        
                        @if($application->certificate_document)
                        <a href="{{ route('admin.applications.download', [$application, 'certificate_document']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                            <i class="ti ti-certificate text-2xl text-green-600"></i>
                            <div>
                                <p class="font-medium text-gray-800">Education Certificate</p>
                                <p class="text-xs text-gray-500">Click to download</p>
                            </div>
                        </a>
                        @endif
                        
                        @if($application->business_license)
                        <a href="{{ route('admin.applications.download', [$application, 'business_license']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                            <i class="ti ti-license text-2xl text-purple-600"></i>
                            <div>
                                <p class="font-medium text-gray-800">Business License</p>
                                <p class="text-xs text-gray-500">Click to download</p>
                            </div>
                        </a>
                        @endif
                        
                        @if($application->tax_certificate)
                        <a href="{{ route('admin.applications.download', [$application, 'tax_certificate']) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                            <i class="ti ti-file-text text-2xl text-red-600"></i>
                            <div>
                                <p class="font-medium text-gray-800">Tax Certificate</p>
                                <p class="text-xs text-gray-500">Click to download</p>
                            </div>
                        </a>
                        @endif
                    </div>
                    @if(!$application->id_document && !$application->certificate_document && !$application->business_license && !$application->tax_certificate)
                        <p class="text-gray-500 text-center py-4">No documents submitted</p>
                    @endif
                </div>
            </div>

            <!-- Admin Actions -->
            @if($application->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-settings"></i> Admin Actions
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                                ✅ Approve Application
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.applications.reject', $application) }}" id="reject-form">
                            @csrf
                            <textarea name="admin_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Reason for rejection... (required)"></textarea>
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
            <div class="bg-red-50 rounded-xl border border-red-200 overflow-hidden">
                <div class="px-6 py-3 bg-red-100 border-b border-red-200">
                    <h3 class="font-semibold text-red-700 flex items-center gap-2">
                        <i class="ti ti-message"></i> Rejection Reason
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $application->admin_notes }}</p>
                    <p class="text-xs text-gray-400 mt-2">Reviewed by {{ $application->reviewer->full_name ?? 'Admin' }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Reject form validation
    document.getElementById('reject-form')?.addEventListener('submit', function(e) {
        const reason = this.querySelector('textarea[name="admin_notes"]').value.trim();
        if (!reason) {
            e.preventDefault();
            alert('Please provide a reason for rejection.');
        }
    });
</script>
@endsection