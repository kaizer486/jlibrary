@extends('layouts.librarian')

@section('title', 'Edit Book')
@section('page-title', 'Edit Book: ' . $book->title)

@section('content')

<div class="max-w-4xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('institution.books.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <!-- Edit Form -->
    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <h3 style="font-size: 1rem; font-weight: 600; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-edit" style="color: #db570a;"></i>
                Edit Book Details
            </h3>
        </div>
        
        <form method="POST" action="{{ route('institution.books.update', $book->id) }}"
              enctype="multipart/form-data" 
              style="padding: 1.5rem;">
            @csrf
            @method('PUT')
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- ========================================== -->
                <!-- BASIC INFORMATION                         -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-info-circle" style="color: #5b21b6;"></i> Basic Information
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <!-- Title -->
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Title</label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;"
                                   placeholder="Book title">
                            @error('title')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Author -->
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Author</label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;"
                                   placeholder="Author name">
                            @error('author')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CATEGORY & DESCRIPTION                    -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-file-description" style="color: #db570a;"></i> Category & Description
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <!-- Category -->
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Category</label>
                            <select name="category" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="">Select Category</option>
                                <option value="fiction" {{ old('category', $book->category) == 'fiction' ? 'selected' : '' }}>Fiction</option>
                                <option value="non-fiction" {{ old('category', $book->category) == 'non-fiction' ? 'selected' : '' }}>Non-Fiction</option>
                                <option value="science" {{ old('category', $book->category) == 'science' ? 'selected' : '' }}>Science</option>
                                <option value="history" {{ old('category', $book->category) == 'history' ? 'selected' : '' }}>History</option>
                                <option value="technology" {{ old('category', $book->category) == 'technology' ? 'selected' : '' }}>Technology</option>
                                <option value="education" {{ old('category', $book->category) == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="biography" {{ old('category', $book->category) == 'biography' ? 'selected' : '' }}>Biography</option>
                                <option value="self-help" {{ old('category', $book->category) == 'self-help' ? 'selected' : '' }}>Self-Help</option>
                                <option value="business" {{ old('category', $book->category) == 'business' ? 'selected' : '' }}>Business</option>
                                <option value="other" {{ old('category', $book->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Status</label>
                            <select name="status" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="pending" {{ old('status', $book->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $book->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $book->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div style="padding: 0 1.25rem 1.25rem 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Description</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 100px; resize: vertical; font-family: inherit;" placeholder="Book description">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- COVER IMAGE                               -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(124, 58, 237, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(124, 58, 237, 0.04); border-bottom: 1px solid rgba(124, 58, 237, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-photo" style="color: #7c3aed;"></i> Cover Image
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        @if($book->cover_image)
                            <div style="margin-bottom: 0.75rem;">
                               <img src="{{ url('media/' . $book->cover_image) }}"
                                     alt="{{ $book->title }}" 
                                     style="width: 8rem; height: 10rem; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e2e0db;">
                                <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Current cover</p>
                            </div>
                        @endif
                        <input type="file" name="cover_image" accept="image/*"
                               style="width: 100%; padding: 0.7rem; background: #faf8f5; border: 1px dashed #d6d2cb; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; cursor: pointer;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Leave empty to keep current image. Max 2MB. Allowed: JPG, PNG, JPEG</p>
                        @error('cover_image')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SHELF LOCATION                            -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(37, 99, 235, 0.04); border-bottom: 1px solid rgba(37, 99, 235, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-map-pin" style="color: #2563eb;"></i> Shelf Location
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Shelf Number</label>
                            <input type="text" name="shelf_number" 
                                   value="{{ old('shelf_number', $book->shelf_number) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="A-01">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Shelf Name</label>
                            <input type="text" name="shelf_name" 
                                   value="{{ old('shelf_name', $book->shelf_name) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="History">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Floor</label>
                            <input type="text" name="floor" 
                                   value="{{ old('floor', $book->floor) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Ground">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Section</label>
                            <input type="text" name="section" 
                                   value="{{ old('section', $book->section) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Non-Fiction">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Column</label>
                            <input type="text" name="column_location" 
                                   value="{{ old('column_location', $book->column_location) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="C-03">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.3rem;">Position</label>
                            <input type="text" name="position" 
                                   value="{{ old('position', $book->position) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Row 5">
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- PRICING                                   -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-coin" style="color: #db570a;"></i> Pricing
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Is Paid?</label>
                            <select name="is_paid" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="0" {{ old('is_paid', $book->is_paid) == '0' ? 'selected' : '' }}>Free</option>
                                <option value="1" {{ old('is_paid', $book->is_paid) == '1' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('is_paid')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Price (TSh)</label>
                            <input type="number" name="price" step="0.01" 
                                   value="{{ old('price', $book->price) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Set to 0.00 for free</p>
                            @error('price')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BOOKSTORE SETTINGS                        -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-shopping-cart" style="color: #5b21b6;"></i> Bookstore Settings
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <!-- Checkbox -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding: 0.75rem 1rem; background: rgba(91, 33, 182, 0.03); border-radius: 0.5rem; border: 1px solid rgba(91, 33, 182, 0.06);">
                            <input type="hidden" name="is_bookstore_item" value="0">
                            <input type="checkbox" name="is_bookstore_item" value="1"
                                   id="is_bookstore_item"
                                   style="width: 1.1rem; height: 1.1rem; accent-color: #5b21b6; cursor: pointer;"
                                   {{ old('is_bookstore_item', $book->is_bookstore_item) ? 'checked' : '' }}>
                            <label for="is_bookstore_item" style="color: #1a1a2e; font-size: 0.875rem; cursor: pointer; font-weight: 500;">
                                This is a bookstore item (available for sale)
                            </label>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Book Type</label>
                                <select name="book_type" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                    <option value="both" {{ old('book_type', $book->book_type) == 'both' ? 'selected' : '' }}>Both (Softcopy + Hardcopy)</option>
                                    <option value="softcopy" {{ old('book_type', $book->book_type) == 'softcopy' ? 'selected' : '' }}>Softcopy Only</option>
                                    <option value="hardcopy" {{ old('book_type', $book->book_type) == 'hardcopy' ? 'selected' : '' }}>Hardcopy Only</option>
                                </select>
                                @error('book_type')
                                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Stock Quantity (Hardcopy)</label>
                                <input type="number" name="stock_quantity" 
                                       value="{{ old('stock_quantity', $book->stock_quantity ?? 0) }}"
                                       style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0">
                                @error('stock_quantity')
                                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Softcopy Price (TSh)</label>
                                <input type="number" name="softcopy_price" step="0.01" 
                                       value="{{ old('softcopy_price', $book->softcopy_price) }}"
                                       style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                                @error('softcopy_price')
                                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Hardcopy Price (TSh)</label>
                                <input type="number" name="hardcopy_price" step="0.01" 
                                       value="{{ old('hardcopy_price', $book->hardcopy_price) }}"
                                       style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                                @error('hardcopy_price')
                                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- FILES                                      -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(124, 58, 237, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(124, 58, 237, 0.04); border-bottom: 1px solid rgba(124, 58, 237, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-file" style="color: #7c3aed;"></i> Files
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">PDF File</label>
                            @if($book->file_path)
                                <p style="font-size: 0.75rem; color: #065f46; margin-bottom: 0.5rem;">Current PDF uploaded</p>
                            @endif
                            <input type="file" name="file" accept=".pdf"
                                   style="width: 100%; padding: 0.7rem; background: #faf8f5; border: 1px dashed #d6d2cb; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; cursor: pointer;">
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Max 10MB. Leave empty to keep current</p>
                            @error('file')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Total Pages</label>
                            <input type="number" name="total_pages" 
                                   value="{{ old('total_pages', $book->total_pages) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="100">
                            @error('total_pages')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 1rem; padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid #e2e0db;">
                <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                    <i class="ti ti-device-floppy"></i> Update Book
                </button>
                <a href="{{ route('institution.books.index') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; cursor: pointer; text-decoration: none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>

<style>
    /* ========================================== */
    /* CLEAN FORM STYLES                         */
    /* ========================================== */

    a[style*="Back to Books"]:hover {
        color: #4c1d95 !important;
    }
    
    input:focus, 
    textarea:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.1) !important;
        background: white !important;
    }
    
    input:hover, 
    textarea:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    input[type="file"]:hover {
        border-color: #5b21b6 !important;
        background: white !important;
    }
    
    input[type="file"]::-webkit-file-upload-button {
        padding: 0.4rem 1.25rem;
        border: none;
        border-radius: 0.4rem;
        background: #e8e4de;
        color: #1a1a2e;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 0.75rem;
    }
    
    input[type="file"]::-webkit-file-upload-button:hover {
        background: #d6d2cb;
    }
    
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    a[style*="Cancel"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    div[style*="background: white; border: 1px solid"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: white; border: 1px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    
    select optgroup {
        font-weight: 700;
        color: #1a1a2e;
        background: white;
    }
    
    select option {
        padding: 0.3rem 0.5rem;
        color: #334155;
    }
    
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="grid-template-columns: 1fr 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="padding: 1.25rem"] {
            padding: 1rem !important;
        }
        
        div[style*="display: flex; gap: 1rem; padding-top: 1.5rem;"] {
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