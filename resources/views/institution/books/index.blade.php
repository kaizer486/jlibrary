@extends('layouts.librarian')

@section('title', 'Institution Books')
@section('page-title', 'Institution Books')

@section('content')

<div class="max-w-7xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage books in {{ $institution->name }}</p>
            </div>
            <a href="{{ route('institution.books.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add New Book
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #5b21b6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin: 0;">{{ $stats['total'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total Books</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #065f46; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $stats['approved'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Approved</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(217, 119, 6, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #d97706; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706; margin: 0;">{{ $stats['pending'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Pending</p>
        </div>
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(220, 38, 38, 0.12); border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626; margin: 0;">{{ $stats['rejected'] ?? 0 }}</p>
            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Rejected</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by title or author..." 
                       value="{{ request('search') }}"
                       style="width: 100%; padding: 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;">
            </div>
            <select name="status" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="shelf" style="padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer; min-width: 150px;">
                <option value="">All Shelves</option>
                @foreach($shelves ?? [] as $shelf)
                    <option value="{{ $shelf->code }}" {{ request('shelf') == $shelf->code ? 'selected' : '' }}>
                        {{ $shelf->code }}
                    </option>
                @endforeach
            </select>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.books.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Books Table -->
    @if($books->count() > 0)
        <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="overflow-x-auto">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(91, 33, 182, 0.04); text-align: left; border-bottom: 1px solid #e2e0db;">
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Book</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Author</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Shelf</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Type</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Stock</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Price</th>
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
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" style="width: 2.5rem; height: 3.5rem; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e2e0db;">
                                        @else
                                            <div style="width: 2.5rem; height: 3.5rem; background: #faf8f5; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e0db;">
                                                <i class="ti ti-book" style="color: #5b21b6;"></i>
                                            </div>
                                        @endif
                                        <span style="font-weight: 500; color: #1a1a2e;">{{ Str::limit($book->title, 30) }}</span>
                                    </div>
                                </td>
                                
                                <!-- Author -->
                                <td style="padding: 0.75rem 1rem; color: #4b5563;">{{ $book->author ?? 'Unknown' }}</td>
                                
                                <!-- Shelf -->
                                <td style="padding: 0.75rem 1rem;">
                                    @if($book->shelf_number)
                                        <span style="font-size: 0.7rem; background: rgba(91, 33, 182, 0.08); color: #5b21b6; padding: 0.15rem 0.6rem; border-radius: 9999px;">{{ $book->shelf_number }}</span>
                                    @else
                                        <span style="font-size: 0.7rem; color: #9ca3af;">—</span>
                                    @endif
                                </td>
                                
                                <!-- Book Type -->
                                <td style="padding: 0.75rem 1rem;">
                                    @if($book->is_bookstore_item)
                                        @if($book->book_type === 'softcopy')
                                            <span style="font-size: 0.7rem; background: rgba(37, 99, 235, 0.08); color: #2563eb; padding: 0.15rem 0.6rem; border-radius: 9999px;">Softcopy</span>
                                        @elseif($book->book_type === 'hardcopy')
                                            <span style="font-size: 0.7rem; background: rgba(217, 119, 6, 0.08); color: #d97706; padding: 0.15rem 0.6rem; border-radius: 9999px;">Hardcopy</span>
                                        @else
                                            <span style="font-size: 0.7rem; background: rgba(91, 33, 182, 0.08); color: #5b21b6; padding: 0.15rem 0.6rem; border-radius: 9999px;">Both</span>
                                        @endif
                                    @else
                                        <span style="font-size: 0.7rem; color: #9ca3af;">—</span>
                                    @endif
                                </td>
                                
                                <!-- Stock -->
                                <td style="padding: 0.75rem 1rem;">
                                    @if($book->is_bookstore_item)
                                        <span style="font-weight: 500; color: {{ ($book->stock_quantity ?? 0) > 0 ? '#065f46' : '#dc2626' }};">
                                            {{ $book->stock_quantity ?? 0 }}
                                        </span>
                                    @else
                                        <span style="font-size: 0.7rem; color: #9ca3af;">—</span>
                                    @endif
                                </td>
                                
                                <!-- Price -->
                                <td style="padding: 0.75rem 1rem;">
                                    @if($book->is_bookstore_item)
                                        @if($book->softcopy_price && $book->hardcopy_price)
                                            <span style="font-size: 0.7rem; color: #6b7280;">Soft: TSh {{ number_format($book->softcopy_price, 2) }}</span>
                                            <br>
                                            <span style="font-size: 0.7rem; color: #6b7280;">Hard: TSh {{ number_format($book->hardcopy_price, 2) }}</span>
                                        @elseif($book->softcopy_price)
                                            <span style="font-size: 0.75rem; color: #2563eb;">TSh {{ number_format($book->softcopy_price, 2) }}</span>
                                        @elseif($book->hardcopy_price)
                                            <span style="font-size: 0.75rem; color: #d97706;">TSh {{ number_format($book->hardcopy_price, 2) }}</span>
                                        @else
                                            <span style="font-size: 0.75rem; color: #6b7280;">Free</span>
                                        @endif
                                    @elseif($book->is_paid)
                                        <span style="color: #db570a; font-weight: 500;">TSh {{ number_format($book->price, 2) }}</span>
                                    @else
                                        <span style="color: #065f46; font-weight: 700;">FREE</span>
                                    @endif
                                </td>
                                
                                <!-- Status -->
                                <td style="padding: 0.75rem 1rem;">
                                    @if($book->status === 'approved')
                                        <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; color: #065f46; background: rgba(6, 95, 70, 0.08);">Approved</span>
                                    @elseif($book->status === 'pending')
                                        <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; color: #d97706; background: rgba(217, 119, 6, 0.08);">Pending</span>
                                    @else
                                        <span style="display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; color: #dc2626; background: rgba(220, 38, 38, 0.08);">Rejected</span>
                                    @endif
                                </td>
                                
                                <!-- Actions -->
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <a href="{{ route('institution.books.show', $book) }}" style="color: #5b21b6; transition: color 0.2s; text-decoration: none;" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('institution.books.edit', $book) }}" style="color: #2563eb; transition: color 0.2s; text-decoration: none;" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @if($book->status === 'pending')
                                            <form method="POST" action="{{ route('institution.books.approve', $book) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="color: #065f46; background: none; border: none; cursor: pointer; transition: color 0.2s; padding: 0;" title="Approve">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('institution.books.destroy', $book) }}" 
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
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">No Books Found</h3>
            <p style="color: #9ca3af;">No books have been added to this institution yet.</p>
            <a href="{{ route('institution.books.create') }}" style="display: inline-block; margin-top: 1rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; cursor: pointer;">
                <i class="ti ti-plus"></i> Add Your First Book
            </a>
        </div>
    @endif

</div>

<style>
    /* ========================================== */
    /* CLEAN TABLE STYLES                        */
    /* ========================================== */

    a[style*="Add New Book"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    input:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Clear"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
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
    
    button[title="Approve"]:hover {
        color: #059669 !important;
    }
    
    button[title="Delete"]:hover {
        color: #b91c1c !important;
    }
    
    /* Stats card hover */
    div[style*="border-left: 4px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="border-left: 4px solid"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
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
        form[method="GET"] {
            flex-direction: column !important;
        }
        
        form[method="GET"] > div,
        form[method="GET"] select,
        form[method="GET"] button,
        form[method="GET"] a {
            width: 100% !important;
            min-width: unset !important;
        }
        
        .grid-cols-4 {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>

@endsection