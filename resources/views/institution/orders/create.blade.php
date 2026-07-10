@extends('layouts.librarian')

@section('title', 'Purchase ' . $book->title)

@section('content')

<div style="background: #fff8f0; min-height: 100vh; padding: 2rem 0;">
    <div class="container mx-auto px-4 max-w-2xl">
        
        <div class="mb-6">
            <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
               style="color: #6b7280; transition: color 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none;">
                <i class="ti ti-arrow-left"></i> Back to Book
            </a>
        </div>

        <!-- Main Card -->
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
            
            <!-- Header -->
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin-bottom: 0.25rem;">Purchase "{{ $book->title }}"</h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1.5rem;">by {{ $book->author ?? 'Unknown' }}</p>

            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    <!-- Book Type Selection -->
                    <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-device-mobile" style="color: #5b21b6;"></i> Select Format
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                @if($book->book_type == 'softcopy' || $book->book_type == 'both')
                                    <label style="background: #faf8f5; border: 2px solid #e2e0db; border-radius: 0.75rem; padding: 1rem; cursor: pointer; transition: all 0.2s; text-align: center; hover:border-color: #5b21b6;">
                                        <input type="radio" name="book_type" value="softcopy" class="hidden" required>
                                        <div>
                                            <i class="ti ti-device-mobile" style="font-size: 1.5rem; color: #5b21b6;"></i>
                                            <p style="color: #1a1a2e; font-weight: 500; margin-top: 0.25rem;">Softcopy</p>
                                            <p style="color: #db570a; font-weight: 700;">TSh {{ number_format($book->softcopy_price ?? 0, 2) }}</p>
                                        </div>
                                    </label>
                                @endif
                                
                                @if($book->book_type == 'hardcopy' || $book->book_type == 'both')
                                    <label style="background: #faf8f5; border: 2px solid #e2e0db; border-radius: 0.75rem; padding: 1rem; cursor: pointer; transition: all 0.2s; text-align: center; hover:border-color: #5b21b6;">
                                        <input type="radio" name="book_type" value="hardcopy" class="hidden" required>
                                        <div>
                                            <i class="ti ti-book" style="font-size: 1.5rem; color: #5b21b6;"></i>
                                            <p style="color: #1a1a2e; font-weight: 500; margin-top: 0.25rem;">Hardcopy</p>
                                            <p style="color: #db570a; font-weight: 700;">TSh {{ number_format($book->hardcopy_price ?? 0, 2) }}</p>
                                        </div>
                                    </label>
                                @endif
                            </div>
                            @error('book_type') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Quantity -->
                    <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-numbers" style="color: #db570a;"></i> Quantity
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <input type="number" name="quantity" value="1" min="1"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
                            @error('quantity') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Shipping Address (for hardcopy) -->
                    <div id="shipping-address-container" style="background: white; border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; overflow: hidden; display: none;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(6, 95, 70, 0.04); border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-map-pin" style="color: #065f46;"></i> Shipping Address
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <textarea name="shipping_address" rows="3" 
                                      style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 80px; resize: vertical; font-family: inherit;"
                                      placeholder="Enter your shipping address..."></textarea>
                            @error('shipping_address') 
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div style="background: white; border: 1px solid rgba(124, 58, 237, 0.12); border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.6rem 1.25rem; background: rgba(124, 58, 237, 0.04); border-bottom: 1px solid rgba(124, 58, 237, 0.08);">
                            <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="ti ti-notes" style="color: #7c3aed;"></i> Special Notes
                            </span>
                        </div>
                        
                        <div style="padding: 1.25rem;">
                            <textarea name="notes" rows="2" 
                                      style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 60px; resize: vertical; font-family: inherit;"
                                      placeholder="Any special requests?"></textarea>
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div style="background: rgba(219, 87, 10, 0.04); border: 1px solid rgba(219, 87, 10, 0.08); border-radius: 0.75rem; padding: 1rem;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Total Amount</p>
                        <p style="font-size: 1.75rem; font-weight: 700; color: #db570a; margin: 0;" id="total-display">TSh 0.00</p>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e0db;">
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-shopping-cart"></i> Place Order
                    </button>
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
                       style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="book_type"]').forEach(el => {
    el.addEventListener('change', updateTotal);
});
document.querySelector('input[name="quantity"]')?.addEventListener('input', updateTotal);

function updateTotal() {
    const type = document.querySelector('input[name="book_type"]:checked');
    const qty = parseInt(document.querySelector('input[name="quantity"]')?.value || 1);
    
    if (!type) return;
    
    // Get price from selected type
    const label = type.closest('label');
    const priceText = label.querySelector('p:last-child')?.textContent || '0';
    const price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
    
    const total = price * qty;
    document.getElementById('total-display').textContent = 'TSh ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Show shipping address only for hardcopy
document.querySelectorAll('input[name="book_type"]').forEach(el => {
    el.addEventListener('change', function() {
        const container = document.getElementById('shipping-address-container');
        if (this.value === 'hardcopy') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });
});

// Initial state
document.getElementById('shipping-address-container').style.display = 'none';
</script>

<style>
    /* ========================================== */
    /* PURCHASE PAGE STYLES                      */
    /* ========================================== */

    a[style*="Back to Book"]:hover {
        color: #1a1a2e !important;
    }
    
    input:focus, 
    textarea:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    textarea:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    /* Radio button labels - checked state */
    input[type="radio"]:checked + div {
        border-color: #5b21b6 !important;
        background: rgba(91, 33, 182, 0.04) !important;
    }
    
    label:has(input[type="radio"]:checked) {
        border-color: #5b21b6 !important;
        background: rgba(91, 33, 182, 0.04) !important;
    }
    
    label:has(input[type="radio"]):hover {
        border-color: #5b21b6 !important;
    }
    
    /* Submit button hover */
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(219, 87, 10, 0.35);
    }
    
    /* Cancel button hover */
    a[style*="Cancel"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Card hover effect */
    div[style*="background: white; border: 1px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; gap: 0.75rem; margin-top: 1.5rem;"] {
            flex-direction: column !important;
        }
        
        button[type="submit"],
        a[style*="Cancel"] {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection