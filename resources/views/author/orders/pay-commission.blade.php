@extends('layouts.author')

@section('title', 'Pay Commission - Order ' . $order->id)
@section('page-title', 'Pay Commission')

@section('content')

<div style="position: fixed; inset: 0; background: #f3f4f6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">

        <!-- Flash -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                <i class="ti ti-circle-check text-2xl text-emerald-500"></i>
                <p class="text-emerald-800 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Header card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-8 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="ti ti-percentage text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold">Platform Commission Due</h1>
                <p class="text-slate-300 text-sm mt-2">Order {{ $order->book->book_code ?? $order->id }}</p>
            </div>

            <div class="p-8">
                <!-- Amount Display -->
                <div class="text-center mb-8">
                    <p class="text-sm text-slate-500 uppercase tracking-wide font-semibold mb-1">Amount to Pay</p>
                    <p class="text-5xl font-black text-slate-800">TSh {{ number_format($order->platform_commission, 0) }}</p>
                </div>

                <!-- Payment Details -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 mb-8">
                    <p class="text-sm font-bold text-slate-800 mb-3 uppercase tracking-wide">Send Payment To:</p>
                    <p class="font-semibold text-slate-800 text-lg mb-3">JOSIAH NASHON MURIMI</p>
                    <ul class="text-sm text-slate-600 space-y-2 font-mono">
                        <li>• Lipa M-Pesa: <strong class="text-slate-900">35751979</strong></li>
                        <li>• Halopesa: <strong class="text-slate-900">0613916153</strong></li>
                        <li>• NBC Bank: <strong class="text-slate-900">090204008487</strong></li>
                        <li>• CRDB Bank: <strong class="text-slate-900">0152746921900</strong></li>
                        <li>• NMB Bank: <strong class="text-slate-900">53010044340</strong></li>
                        <li>• Selcom Pay: <strong class="text-slate-900">0766408259</strong></li>
                    </ul>
                </div>

                <!-- Submission Form -->
                <form action="{{ route('author.orders.pay-commission', $order->id) }}" method="POST">
                    @csrf
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Which method did you use? <span class="text-red-500">*</span></label>
                            <select name="payment_method" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-800 focus:border-slate-800 bg-white">
                                <option value="">-- Select Method --</option>
                                <option value="lipa_mpesa">Lipa M-Pesa</option>
                                <option value="halopesa">Halopesa</option>
                                <option value="nbc_bank">NBC Bank</option>
                                <option value="crdb_bank">CRDB Bank</option>
                                <option value="nmb_bank">NMB Bank</option>
                                <option value="selcom_pay">Selcom Pay</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Receipt / Reference Code <span class="text-red-500">*</span></label>
                            <input type="text" name="transaction_id" required placeholder="e.g. 123456789" 
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-slate-800 focus:border-slate-800 uppercase font-mono">
                            <p class="text-[11px] text-slate-500 mt-1">Just the short code from your SMS.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8">
                        <a href="{{ route('author.orders.show', $order->id) }}" class="px-6 py-3 border border-slate-300 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl font-semibold hover:bg-slate-800 transition shadow-md">
                            Submit for Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection