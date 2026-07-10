@extends('layouts.librarian')

@section('title', 'Bookstore Dashboard')
@section('page-title', '📖 Bookstore Dashboard')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Welcome -->
    <div class="rounded-2xl p-6 mb-8" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid rgba(219, 87, 10, 0.2); box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold" style="color: #1a1a2e;">
                    Welcome back, {{ auth()->user()->full_name }}
                </h2>
                <p style="color: #6b7280; margin-top: 0.25rem;">Manage your bookstore inventory and sales</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('institution.books.create') }}" class="btn-library" style="background: linear-gradient(135deg, #db570a, #e87a2a); color: white; border: none; padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; text-decoration: none; box-shadow: 0 4px 16px rgba(219, 87, 10, 0.3);">
                    <i class="ti ti-plus"></i> Add Product
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="library-stat" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(91, 33, 182, 0.15); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-left: 4px solid #5b21b6;">
            <p class="number" style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total_books'] ?? 0 }}</p>
            <p class="label" style="color: #6b7280; font-size: 0.875rem; margin: 0;">📚 Total Products</p>
        </div>
        <div class="library-stat" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(6, 95, 70, 0.15); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-left: 4px solid #065f46;">
            <p class="number" style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['in_stock'] ?? 0 }}</p>
            <p class="label" style="color: #6b7280; font-size: 0.875rem; margin: 0;">✅ In Stock</p>
        </div>
        <div class="library-stat" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(251, 191, 36, 0.15); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-left: 4px solid #fbbf24;">
            <p class="number" style="font-size: 1.5rem; font-weight: 700; color: #fbbf24; margin: 0;">{{ $stats['today_sales'] ?? 0 }}</p>
            <p class="label" style="color: #6b7280; font-size: 0.875rem; margin: 0;">💰 Sold Today</p>
        </div>
        <div class="library-stat" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 2px solid rgba(96, 165, 250, 0.15); border-radius: 0.75rem; padding: 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border-left: 4px solid #60a5fa;">
            <p class="number" style="font-size: 1.5rem; font-weight: 700; color: #60a5fa; margin: 0;">{{ $stats['total_customers'] ?? 0 }}</p>
            <p class="label" style="color: #6b7280; font-size: 0.875rem; margin: 0;">👥 Customers</p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="library-card" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid rgba(91, 33, 182, 0.15); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
            <h3 class="font-semibold text-lg mb-4 flex items-center gap-2" style="color: #1a1a2e;">
                <i class="ti ti-shopping-cart" style="color: #5b21b6;"></i> Recent Orders
            </h3>
            @if(isset($recentOrders) && $recentOrders->count() > 0)
                @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
                        <div>
                            <p class="text-sm" style="color: #1a1a2e; font-weight: 500;">Order #{{ $order->id }}</p>
                            <p class="text-xs" style="color: #6b7280;">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-medium" style="color: #065f46;">
                            TSh {{ number_format($order->total, 2) }}
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color: #6b7280; font-size: 0.875rem;">No recent orders</p>
            @endif
        </div>

        <!-- Low Stock -->
        <div class="library-card" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid rgba(251, 191, 36, 0.15); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
            <h3 class="font-semibold text-lg mb-4 flex items-center gap-2" style="color: #1a1a2e;">
                <i class="ti ti-alert-triangle" style="color: #fbbf24;"></i> Low Stock Alert
            </h3>
            @if(isset($lowStockBooks) && $lowStockBooks->count() > 0)
                @foreach($lowStockBooks as $book)
                    <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
                        <div>
                            <p class="text-sm" style="color: #1a1a2e; font-weight: 500;">{{ $book->title }}</p>
                            <p class="text-xs" style="color: #6b7280;">{{ $book->category ?? 'Uncategorized' }}</p>
                        </div>
                        <span class="text-sm font-medium" style="color: #fbbf24;">
                            {{ $book->quantity ?? 0 }} left
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color: #6b7280; font-size: 0.875rem;">All products are well stocked ✅</p>
            @endif
        </div>
    </div>

</div>

<style>
    /* Hover effect for Add Product button */
    .btn-library:hover {
        filter: brightness(0.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(219, 87, 10, 0.4);
    }
    
    /* Hover effects for stat cards */
    .library-stat {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    
    .library-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        border-color: rgba(91, 33, 182, 0.3) !important;
    }
    
    /* Hover effects for library cards */
    .library-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .library-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        border-color: rgba(91, 33, 182, 0.3) !important;
    }
    
    /* Specific hover for low stock card */
    .library-card:last-child:hover {
        border-color: rgba(251, 191, 36, 0.3) !important;
    }
</style>

@endsection