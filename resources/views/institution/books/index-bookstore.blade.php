@extends('layouts.librarian')

@section('title', 'Bookstore Inventory')
@section('page-title', 'Bookstore Inventory')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage your bookstore inventory</p>
        </div>
        <a href="{{ route('institution.books.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Books</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $books->where('status', 'active')->count() }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">In Stock</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706; margin: 0;">{{ $books->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0)->count() }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Low Stock</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ $books->where('status', 'out_of_stock')->count() }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Out of Stock</p>
        </div>
    </div>

    <!-- Books Table -->
    @if($books->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Book</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Price</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Stock</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Sold</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #e2e0db;">
                        @foreach($books as $book)
                            <tr style="transition: background 0.2s; border-bottom: 1px solid #f0ede8;">
                                <!-- Book -->
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @if($book->cover_image)
                                            <img src="{{ url('media/' . $book->cover_image) }}" 
                                                 alt="{{ $book->title }}" 
                                                 style="width: 3rem; height: 4rem; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e2e0db;">
                                        @else
                                            <div style="width: 3rem; height: 4rem; background: #faf8f5; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e0db;">
                                                <i class="ti ti-book" style="color: #5b21b6; font-size: 1.25rem;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p style="font-weight: 500; color: #1a1a2e; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">{{ Str::limit($book->title, 30) }}</p>
                                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">{{ $book->author ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: #db570a;">TSh {{ number_format($book->price, 2) }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="font-weight: 500; color: {{ $book->stock_quantity <= 0 ? '#dc2626' : ($book->stock_quantity <= 5 ? '#d97706' : '#065f46') }};">
                                        {{ $book->stock_quantity }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; color: #4b5563;">{{ $book->sold_count ?? 0 }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; 
                                        @if($book->status === 'active') color: #065f46; background: rgba(6, 95, 70, 0.08);
                                        @elseif($book->status === 'out_of_stock') color: #dc2626; background: rgba(220, 38, 38, 0.08);
                                        @else color: #6b7280; background: rgba(0,0,0,0.04); @endif">
                                        {{ ucfirst(str_replace('_', ' ', $book->status)) }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <a href="{{ route('institution.books.show', $book->id) }}" 
                                           style="color: #5b21b6; transition: color 0.2s; text-decoration: none;" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('institution.books.edit', $book->id) }}" 
                                           style="color: #2563eb; transition: color 0.2s; text-decoration: none;" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('institution.books.destroy', $book->id) }}" 
                                              onsubmit="return confirm('Delete this book?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #dc2626; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <i class="ti ti-books" style="font-size: 3.5rem; color: #d6d2cb; display: block; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Books in Inventory</h3>
            <p style="color: #9ca3af;">Add your first book to start selling.</p>
            <a href="{{ route('institution.books.create') }}" style="display: inline-block; margin-top: 1rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add Your First Book
            </a>
        </div>
    @endif

</div>

<style>
    /* ========================================== */
    /* CLEAN TABLE & STATS STYLES                */
    /* ========================================== */

    a[style*="Add New Book"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Stats card hover */
    div[style*="border-left: 4px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="border-left: 4px solid"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Table row hover */
    tbody tr:hover {
        background: rgba(91, 33, 182, 0.03) !important;
    }
    
    /* Action icons hover */
    a[title="View"]:hover {
        color: #4c1d95 !important;
    }
    
    a[title="Edit"]:hover {
        color: #1d4ed8 !important;
    }
    
    button[title="Delete"]:hover {
        color: #b91c1c !important;
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }
    
    .pagination span,
    .pagination a {
        padding: 0.4rem 0.8rem;
        border-radius: 0.4rem;
        border: 1px solid #e2e0db;
        background: white;
        color: #1a1a2e;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        border-color: #db570a;
        background: #faf8f5;
    }
    
    .pagination .active span {
        background: #db570a;
        border-color: #db570a;
        color: white;
    }
    
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
        
        table {
            font-size: 0.75rem !important;
        }
        
        td, th {
            padding: 0.5rem !important;
        }
        
        td div p {
            max-width: 100px !important;
        }
    }
</style>

@endsection