@extends('layouts.librarian')

@section('title', 'Payment Instructions')

@section('content')

<div class="max-w-3xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.subscription.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Subscription
        </a>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(219, 87, 10, 0.04);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-building-bank" style="color: #db570a;"></i> Bank Transfer Instructions
            </h2>
        </div>
        
        <div style="padding: 1.5rem;">
            
            <!-- Subscription Info -->
            <div style="background: rgba(30, 58, 95, 0.03); border: 1px solid rgba(30, 58, 95, 0.06); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Plan</p>
                        <p style="color: #1a1a2e; font-weight: 600; margin: 0; text-transform: capitalize;">{{ $subscription->plan }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Amount</p>
                        <p style="color: #db570a; font-weight: 700; margin: 0;">TSh {{ number_format($subscription->amount, 2) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Status</p>
                        <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; color: #d97706; background: rgba(217, 119, 6, 0.08);">
                            Pending Payment
                        </span>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Reference</p>
                        <p style="color: #1a1a2e; font-family: monospace; font-size: 0.875rem; margin: 0;">{{ $subscription->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div style="background: rgba(6, 95, 70, 0.03); border: 1px solid rgba(6, 95, 70, 0.08); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                <h3 style="color: #065f46; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.95rem;">
                    <i class="ti ti-info-circle"></i> Bank Details
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <span style="color: #6b7280; font-size: 0.875rem;">Bank Name</span>
                        <span style="color: #1a1a2e; font-weight: 600;">CRDB Bank</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <span style="color: #6b7280; font-size: 0.875rem;">Account Name</span>
                        <span style="color: #1a1a2e; font-weight: 600;">JLIBRARY Subscription</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <span style="color: #6b7280; font-size: 0.875rem;">Account Number</span>
                        <span style="color: #1a1a2e; font-family: monospace; font-weight: 600;">0123456789</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <span style="color: #6b7280; font-size: 0.875rem;">Branch</span>
                        <span style="color: #1a1a2e; font-weight: 600;">Dar es Salaam</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="color: #6b7280; font-size: 0.875rem;">SWIFT Code</span>
                        <span style="color: #1a1a2e; font-weight: 600;">CRDBTZTZ</span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div style="background: rgba(37, 99, 235, 0.03); border: 1px solid rgba(37, 99, 235, 0.08); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                <h3 style="color: #2563eb; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.95rem;">
                    <i class="ti ti-list-check"></i> Steps to Complete Payment
                </h3>
                <ol style="display: flex; flex-direction: column; gap: 0.75rem; color: #4b5563; padding-left: 0; margin: 0; list-style: none;">
                    <li style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(37, 99, 235, 0.08); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;">1</span>
                        <span>Transfer the exact amount of <strong style="color: #1a1a2e;">TSh {{ number_format($subscription->amount, 2) }}</strong> to the bank account above</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(37, 99, 235, 0.08); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;">2</span>
                        <span>Use your <strong style="color: #1a1a2e;">Subscription ID ({{ $subscription->id }})</strong> as the reference/description</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(37, 99, 235, 0.08); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;">3</span>
                        <span>After payment, <strong style="color: #1a1a2e;">upload the payment confirmation</strong> below</span>
                    </li>
                    <li style="display: flex; gap: 0.75rem; align-items: flex-start;">
                        <span style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background: rgba(37, 99, 235, 0.08); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;">4</span>
                        <span>Wait for <strong style="color: #1a1a2e;">Super Admin</strong> to verify and activate your subscription</span>
                    </li>
                </ol>
            </div>

            <!-- Upload Confirmation -->
            <div style="background: rgba(30, 58, 95, 0.03); border: 1px solid rgba(30, 58, 95, 0.06); border-radius: 0.75rem; padding: 1.5rem;">
                <h3 style="color: #1a1a2e; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.95rem;">
                    <i class="ti ti-upload" style="color: #1e3a5f;"></i> Upload Payment Confirmation
                </h3>
                <form action="{{ route('institution.subscription.upload-payment-proof', $subscription->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="border: 2px dashed rgba(30, 58, 95, 0.15); border-radius: 0.75rem; padding: 1.5rem; text-align: center; transition: all 0.3s ease; cursor: pointer;" id="dropZone">
                        <input type="file" name="payment_proof" id="paymentProof" class="hidden" accept="image/*,.pdf">
                        <i class="ti ti-cloud-upload" style="font-size: 2.5rem; color: #d6d2cb; display: block; margin-bottom: 0.5rem;"></i>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Click or drag to upload payment receipt/screenshot</p>
                        <p style="color: #9ca3af; font-size: 0.7rem; margin-top: 0.25rem;">JPG, PNG, PDF (Max 5MB)</p>
                        <div id="fileName" class="mt-2" style="color: #065f46; font-size: 0.875rem; display: none;"></div>
                    </div>
                    <button type="submit" style="width: 100%; margin-top: 1rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.7rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-check"></i> Submit Payment Confirmation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('dropZone').addEventListener('click', function() {
    document.getElementById('paymentProof').click();
});

document.getElementById('paymentProof').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = document.getElementById('fileName');
        fileName.textContent = 'Selected: ' + file.name;
        fileName.style.display = 'block';
    }
});

// Drop zone hover effect
document.getElementById('dropZone').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#db570a';
    this.style.background = 'rgba(219, 87, 10, 0.04)';
});

document.getElementById('dropZone').addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = 'rgba(30, 58, 95, 0.15)';
    this.style.background = 'transparent';
});

document.getElementById('dropZone').addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = 'rgba(30, 58, 95, 0.15)';
    this.style.background = 'transparent';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('paymentProof').files = files;
        const file = files[0];
        const fileName = document.getElementById('fileName');
        fileName.textContent = 'Selected: ' + file.name;
        fileName.style.display = 'block';
    }
});
</script>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Back to Subscription"]:hover {
        color: #4c1d95 !important;
    }
    
    /* Submit button hover */
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Card hover effect */
    div[style*="background: rgba(30, 58, 95, 0.03)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(30, 58, 95, 0.03)"]:hover {
        background: rgba(30, 58, 95, 0.05) !important;
    }
    
    /* Main card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        box-shadow: 0 4px 16px rgba(30, 58, 95, 0.04) !important;
    }
    
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; justify-content: space-between;"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.2rem !important;
        }
    }
</style>

@endsection