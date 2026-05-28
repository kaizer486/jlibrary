@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Apply to become a {{ ucfirst($type) }}</h1>
            <p class="text-purple-200 text-sm">Submit your application and documents for review</p>
        </div>
        
        <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Why do you want to become a {{ ucfirst($type) }}?</label>
                    <textarea name="message" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Tell us about your experience and motivation..."></textarea>
                </div>
                
                @if($type === 'bookseller')
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Business Information</h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                            <input type="text" name="business_name" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TIN Number</label>
                            <input type="text" name="tax_id" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                            <textarea name="business_address" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Required Documents</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">National ID / Passport <span class="text-red-500">*</span></label>
                            <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png" required class="w-full">
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, or PNG (Max 5MB)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Education/Professional Certificate</label>
                            <input type="file" name="certificate_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                        </div>
                        
                        @if($type === 'bookseller')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business License</label>
                            <input type="file" name="business_license" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Certificate</label>
                            <input type="file" name="tax_certificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Submit Application
                </button>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection