@extends('layouts.app')

@section('title', 'Payment Instructions')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-credit-card text-3xl text-orange-600"></i>
            </div>
            <h1 class="text-2xl font-bold" style="color: #1a1a2e;">Payment Instructions</h1>
            <p class="text-sm" style="color: #6b7280;">Complete your purchase using {{ ucfirst($payment->method) }}</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3">
                <i class="ti ti-info-circle text-yellow-600 text-xl"></i>
                <div>
                    <p class="font-semibold" style="color: #92400e;">Payment Reference</p>
                    <p class="text-sm font-mono" style="color: #78350f;">{{ $payment->reference }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="p-4 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                <p class="text-xs" style="color: #6b7280;">Amount</p>
                <p class="text-xl font-bold" style="color: #db570a;">TSh {{ number_format($payment->amount, 2) }}</p>
            </div>
            <div class="p-4 rounded-xl text-center" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                <p class="text-xs" style="color: #6b7280;">Status</p>
                <p class="text-xl font-bold" style="color: #d97706;">Pending</p>
            </div>
        </div>

        <!-- Payment Instructions based on method -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <h4 class="font-semibold text-sm" style="color: #1e3a5f;">
                <i class="ti ti-info-circle"></i> 
                @if($payment->method === 'mpesa')
                    M-Pesa Payment Instructions
                @elseif($payment->method === 'tigopesa')
                    TigoPesa Payment Instructions
                @elseif($payment->method === 'halopesa')
                    HaloPesa Payment Instructions
                @elseif($payment->method === 'pesapal')
                    PesaPal Payment Instructions
                @elseif($payment->method === 'bank')
                    Bank Transfer Instructions
                @else
                    Payment Instructions
                @endif
            </h4>
            <ol class="text-sm mt-2 space-y-2" style="color: #4b5563;">
                @if($payment->method === 'mpesa')
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">1.</span>
                        <span>Open your M-Pesa app</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">2.</span>
                        <span>Select <strong>Lipa Na M-Pesa</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">3.</span>
                        <span>Pay to <strong>0754 123 456</strong> (JLibrary)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">4.</span>
                        <span>Amount: <strong>TSh {{ number_format($payment->amount, 2) }}</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">5.</span>
                        <span>Use reference: <strong>{{ $payment->reference }}</strong></span>
                    </li>
                @elseif($payment->method === 'tigopesa')
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">1.</span>
                        <span>Open your TigoPesa app</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">2.</span>
                        <span>Select <strong>Pay</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">3.</span>
                        <span>Pay to <strong>0754 123 456</strong> (JLibrary)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">4.</span>
                        <span>Amount: <strong>TSh {{ number_format($payment->amount, 2) }}</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">5.</span>
                        <span>Use reference: <strong>{{ $payment->reference }}</strong></span>
                    </li>
                @elseif($payment->method === 'halopesa')
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">1.</span>
                        <span>Open your HaloPesa app</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">2.</span>
                        <span>Select <strong>Send Money</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">3.</span>
                        <span>Pay to <strong>0754 123 456</strong> (JLibrary)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">4.</span>
                        <span>Amount: <strong>TSh {{ number_format($payment->amount, 2) }}</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">5.</span>
                        <span>Use reference: <strong>{{ $payment->reference }}</strong></span>
                    </li>
                @elseif($payment->method === 'pesapal')
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">1.</span>
                        <span>Click the button below to complete payment via PesaPal</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">2.</span>
                        <span>You will be redirected to PesaPal secure payment page</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">3.</span>
                        <span>Complete payment using Mobile Money or Card</span>
                    </li>
                @elseif($payment->method === 'bank')
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">1.</span>
                        <span>Bank: <strong>CRDB Bank</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">2.</span>
                        <span>Account Name: <strong>JLibrary Limited</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">3.</span>
                        <span>Account Number: <strong>1234567890</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">4.</span>
                        <span>Amount: <strong>TSh {{ number_format($payment->amount, 2) }}</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold" style="color: #1e3a5f;">5.</span>
                        <span>Reference: <strong>{{ $payment->reference }}</strong></span>
                    </li>
                @endif
            </ol>
        </div>

        <div class="flex gap-3">
            @if($payment->method === 'pesapal')
                <a href="/payment/pesapal/redirect/{{ $payment->id }}" 
                   class="flex-1 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] text-center"
                   style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white;">
                    <i class="ti ti-credit-card"></i> Pay with PesaPal
                </a>
            @else
                <button onclick="confirmPayment({{ $payment->id }})" 
                        id="confirm-payment-btn"
                        class="flex-1 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white;">
                    <i class="ti ti-check"></i> I've Made the Payment
                </button>
            @endif
            <a href="/book/purchase/{{ $payment->payable_id }}" 
               class="py-3 px-4 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02]"
               style="background: rgba(0,0,0,0.05); color: #6b7280; border: 1px solid rgba(0,0,0,0.1);">
                Cancel
            </a>
        </div>
    </div>
</div>

<script>
function confirmPayment(paymentId) {
    const btn = document.getElementById('confirm-payment-btn');
    if (!confirm('Have you completed the payment?')) {
        return;
    }
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Confirming...';
    btn.style.opacity = '0.7';
    
    fetch('{{ route("payment.confirm") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ payment_id: paymentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.href = '/book/purchase/success/' + paymentId;
        } else {
            alert('❌ ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-check"></i> I\'ve Made the Payment';
            btn.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-check"></i> I\'ve Made the Payment';
        btn.style.opacity = '1';
    });
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection