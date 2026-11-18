@extends('layouts.app')

@section('title', 'Bulk Checkout')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">

    <div class="mb-6">
        <a href="{{ route('library.index', ['format' => 'hardcopy']) }}" class="text-jlibrary-600 hover:text-jlibrary-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Market
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-jlibrary-600 to-purple-700 px-6 py-4 text-white">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <i class="ti ti-shopping-cart"></i> Complete Your Order
            </h1>
            <p class="text-jlibrary-100 text-sm mt-1">Enter your delivery details for all items</p>
        </div>

               <form action="{{ route('library.bulk-checkout.store') }}" method="POST" class="p-6" x-data="checkoutForm()" x-init="init()"
              onsubmit="setTimeout(() => localStorage.removeItem('jlibrary_cart'), 100)"></form></form>
            @csrf
            <input type="hidden" name="items" x-model="itemsJson">

            <!-- Cart Summary -->
            <div class="mb-6">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Order Summary</h2>
                <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                    <template x-for="item in items" :key="item.id">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-14 bg-orange-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                                <template x-if="item.cover">
                                    <img :src="item.cover" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.cover">
                                    <i class="ti ti-book text-orange-500"></i>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-slate-800 line-clamp-1" x-text="item.title"></p>
                                <p class="text-xs text-slate-500">
                                    <span x-text="item.qty"></span> × TSh <span x-text="Number(item.price).toLocaleString()"></span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-slate-800 text-sm">TSh <span x-text="Number(item.qty * item.price).toLocaleString()"></span></p>
                            </div>
                        </div>
                    </template>
                    
                    <div class="border-t pt-3 flex justify-between items-center">
                        <span class="font-semibold text-slate-800">Total</span>
                        <span class="font-bold text-jlibrary-700 text-lg">TSh <span x-text="total().toLocaleString()"></span></span>
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" required value="{{ auth()->user()->full_name }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-jlibrary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="customer_phone" required placeholder="0712 345 678"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-jlibrary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Address <span class="text-red-500">*</span></label>
                    <textarea name="shipping_address" rows="3" required placeholder="Street, city, region, landmark..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-jlibrary-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Any special delivery instructions..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-jlibrary-500"></textarea>
                </div>
            </div>

            @if(session('error'))
                <div class="mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-8 pt-6 border-t flex gap-3">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-jlibrary-600 to-purple-700 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="ti ti-check"></i> Place Order (TSh <span x-text="total().toLocaleString()"></span>)
                </button>
                <button type="button" onclick="localStorage.removeItem('jlibrary_cart'); window.location.href='{{ route('library.index', ['format'=>'hardcopy']) }}'"
                        class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function checkoutForm() {
    return {
        items: [],
        itemsJson: '[]',
        init() {
            try {
                this.items = JSON.parse(localStorage.getItem('jlibrary_cart') || '[]');
                if (this.items.length === 0) {
                    window.location.href = '{{ route('library.index', ['format'=>'hardcopy']) }}';
                }
            } catch(e) { this.items = []; }
            
            this.$watch('items', () => {
                this.itemsJson = JSON.stringify(this.items);
            });
            this.itemsJson = JSON.stringify(this.items);
        },
        total() {
            return this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
        }
    }
}
</script>
@endsection