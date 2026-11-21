@extends('layouts.super-admin')

@section('title', 'Commission Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-percentage"></i> Platform Commission Settings
            </h1>
            <p class="text-yellow-100 text-sm mt-1">Set the global percentage that sellers pay per sale</p>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2">
                    <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('super-admin.commission-settings.update') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Global Commission Percentage</label>
                    <div class="relative">
                        <input type="number" name="percentage" step="0.1" min="0" max="100" 
                               value="{{ $percentage }}" required
                               class="w-full pl-4 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg font-semibold">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold text-lg">%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="ti ti-info-circle"></i> This percentage will be automatically calculated on all future hardcopy orders. 
                        (e.g., if set to 10% and a book is sold for TSh 10,000, the seller owes TSh 1,000).
                    </p>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Save Settings
                </button>
            </form>
        </div>
    </div>

</div>
@endsection