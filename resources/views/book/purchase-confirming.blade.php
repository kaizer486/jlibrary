@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 max-w-lg text-center">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="w-16 h-16 mx-auto mb-4 border-4 border-jlibrary-600 border-t-transparent rounded-full animate-spin"></div>
        <h1 class="text-xl font-bold text-gray-800">Confirming your payment…</h1>
        <p class="text-sm text-gray-500 mt-2">This usually takes a few seconds. Please don't close this page.</p>
        <p id="status-note" class="text-xs text-gray-400 mt-4"></p>
    </div>
</div>

<script>
    const paymentId = {{ $payment->id }};
    const statusUrl = "{{ route('book.purchase.pesapal.status', $payment->id) }}";
    let attempts = 0;
    const maxAttempts = 15; // ~30 seconds at 2s intervals

    function poll() {
        attempts++;
        fetch(statusUrl)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'completed' && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (data.status === 'failed') {
                    document.getElementById('status-note').textContent =
                        'Payment could not be confirmed. If you completed it, contact support with reference {{ $payment->reference }}.';
                    return;
                }
                if (attempts >= maxAttempts) {
                    document.getElementById('status-note').innerHTML =
                        'Still waiting on confirmation — <a href="' + window.location.href + '" class="underline">refresh manually</a> or check your library shortly.';
                    return;
                }
                setTimeout(poll, 2000);
            })
            .catch(() => setTimeout(poll, 2000));
    }

    poll();
</script>
@endsection