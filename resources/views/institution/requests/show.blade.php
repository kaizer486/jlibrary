@extends('layouts.app')

@section('title', 'Request Details')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="mb-6">
            <a href="{{ route('institution.my-requests') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to My Requests
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-white">Request Details</h1>
                        <p class="text-blue-100 text-sm">Institution Creation Request</p>
                    </div>
                    {!! $request->status_badge !!}
                </div>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Institution Name</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $request->name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Type</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $request->type_label }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Email</p>
                        <p class="text-gray-800">{{ $request->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Phone</p>
                        <p class="text-gray-800">{{ $request->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Location</p>
                        <p class="text-gray-800">
                            @if($request->city || $request->region)
                                {{ $request->city ?? '' }}{{ $request->city && $request->region ? ', ' : '' }}{{ $request->region ?? 'N/A' }}
                            @else
                                Not provided
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Submitted</p>
                        <p class="text-gray-800">{{ $request->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>

                @if($request->address)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Address</p>
                        <p class="text-gray-800">{{ $request->address }}</p>
                    </div>
                @endif

                @if($request->website)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Website</p>
                        <a href="{{ $request->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $request->website }}</a>
                    </div>
                @endif

                @if($request->description)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Description</p>
                        <p class="text-gray-800">{{ $request->description }}</p>
                    </div>
                @endif

                @if($request->motivation)
                    <div class="mt-4 bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-xs text-blue-600 uppercase font-semibold">Motivation</p>
                        <p class="text-gray-800">{{ $request->motivation }}</p>
                    </div>
                @endif

                @if($request->document_path)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Supporting Document</p>
                        <a href="{{ url('media/' . $request->document_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2">
                            <i class="ti ti-file"></i> Download Document
                        </a>
                    </div>
                @endif

                @if($request->status === 'rejected' && $request->rejection_reason)
                    <div class="mt-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <p class="text-xs text-red-600 uppercase font-semibold">Rejection Reason</p>
                        <p class="text-red-700">{{ $request->rejection_reason }}</p>
                    </div>
                @endif

                @if($request->status === 'approved' && $request->approved_at)
                    <div class="mt-4 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <p class="text-xs text-green-600 uppercase font-semibold">Approved</p>
                        <p class="text-green-700">Approved on {{ $request->approved_at->format('F d, Y h:i A') }}</p>
                        <p class="text-sm text-green-600">You are now the Institution Admin of {{ $request->name }}!</p>
                    </div>
                @endif

                @if($request->status === 'pending')
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">
                            <i class="ti ti-clock"></i> Your request is being reviewed by the Super Admin.
                            You will be notified once a decision is made.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection