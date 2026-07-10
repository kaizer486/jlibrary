@extends('layouts.librarian')

@section('title', 'Add New Book')
@section('page-title', 'Add New Book')

@section('content')

<div class="max-w-4xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <a href="{{ route('institution.books.index') }}" style="color: #5b21b6; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; font-weight: 500;">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="rounded-2xl overflow-hidden" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid rgba(91, 33, 182, 0.12); box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <!-- Header -->
        <div style="padding: 1.25rem 1.5rem; border-bottom: 2px solid rgba(91, 33, 182, 0.08); background: rgba(91, 33, 182, 0.04);">
            <h1 style="font-size: 1.25rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="ti ti-book-plus" style="color: #db570a;"></i> Add New Book
            </h1>
            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 1.75rem;">
                @if($institution->type === 'bookstore')
                    Add a new book to your bookstore inventory
                @else
                    Add a new book to your institution library
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('institution.books.store') }}" enctype="multipart/form-data" style="padding: 1.5rem;">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- ========================================== -->
                <!-- 1. BASIC INFORMATION                       -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-info-circle" style="color: #5b21b6;"></i> Basic Information
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;"
                                   placeholder="Enter book title">
                            @error('title')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Author</label>
                            <input type="text" name="author" value="{{ old('author') }}" required
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;"
                                   placeholder="Enter author name">
                            @error('author')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 2. DESCRIPTION                             -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.04); border-bottom: 1px solid rgba(219, 87, 10, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-file-description" style="color: #db570a;"></i> Description
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem;">
                        <textarea name="description" rows="4" style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; min-height: 100px; resize: vertical; font-family: inherit;" placeholder="Enter book description">{{ old('description') }}</textarea>
                        @error('description')
                            <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 3. CATEGORY & STATUS                       -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(6, 95, 70, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(6, 95, 70, 0.04); border-bottom: 1px solid rgba(6, 95, 70, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-tags" style="color: #065f46;"></i> Category & Status
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Category</label>
                            <select name="category" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="">Select Category</option>
                                <optgroup label="Technology & Computing">
                                    <option value="computer_science" {{ old('category') == 'computer_science' ? 'selected' : '' }}>Computer Science & Information Technology</option>
                                    <option value="artificial_intelligence" {{ old('category') == 'artificial_intelligence' ? 'selected' : '' }}>Artificial Intelligence & Data Science</option>
                                    <option value="engineering" {{ old('category') == 'engineering' ? 'selected' : '' }}>Engineering & Technology</option>
                                </optgroup>
                                <optgroup label="Sciences">
                                    <option value="mathematics" {{ old('category') == 'mathematics' ? 'selected' : '' }}>Mathematics & Statistics</option>
                                    <option value="physical_sciences" {{ old('category') == 'physical_sciences' ? 'selected' : '' }}>Physical Sciences</option>
                                    <option value="biological_sciences" {{ old('category') == 'biological_sciences' ? 'selected' : '' }}>Biological Sciences</option>
                                    <option value="health_sciences" {{ old('category') == 'health_sciences' ? 'selected' : '' }}>Health & Medical Sciences</option>
                                    <option value="public_health" {{ old('category') == 'public_health' ? 'selected' : '' }}>Public Health</option>
                                    <option value="agriculture" {{ old('category') == 'agriculture' ? 'selected' : '' }}>Agriculture & Veterinary Sciences</option>
                                    <option value="environmental_sciences" {{ old('category') == 'environmental_sciences' ? 'selected' : '' }}>Environmental & Earth Sciences</option>
                                </optgroup>
                                <optgroup label="Business & Economics">
                                    <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business & Management</option>
                                    <option value="economics" {{ old('category') == 'economics' ? 'selected' : '' }}>Economics & Finance</option>
                                    <option value="accounting" {{ old('category') == 'accounting' ? 'selected' : '' }}>Accounting</option>
                                    <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="entrepreneurship" {{ old('category') == 'entrepreneurship' ? 'selected' : '' }}>Entrepreneurship</option>
                                </optgroup>
                                <optgroup label="Law & Education">
                                    <option value="law" {{ old('category') == 'law' ? 'selected' : '' }}>Law</option>
                                    <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                </optgroup>
                                <optgroup label="Social Sciences">
                                    <option value="social_sciences" {{ old('category') == 'social_sciences' ? 'selected' : '' }}>Social Sciences</option>
                                    <option value="psychology" {{ old('category') == 'psychology' ? 'selected' : '' }}>Psychology</option>
                                    <option value="political_science" {{ old('category') == 'political_science' ? 'selected' : '' }}>Political Science & Public Administration</option>
                                </optgroup>
                                <optgroup label="Humanities">
                                    <option value="philosophy" {{ old('category') == 'philosophy' ? 'selected' : '' }}>Philosophy</option>
                                    <option value="languages" {{ old('category') == 'languages' ? 'selected' : '' }}>Languages & Linguistics</option>
                                    <option value="literature" {{ old('category') == 'literature' ? 'selected' : '' }}>Literature</option>
                                    <option value="history" {{ old('category') == 'history' ? 'selected' : '' }}>History & Archaeology</option>
                                    <option value="geography" {{ old('category') == 'geography' ? 'selected' : '' }}>Geography & Tourism</option>
                                    <option value="religion" {{ old('category') == 'religion' ? 'selected' : '' }}>Religion & Theology</option>
                                </optgroup>
                                <optgroup label="Arts & Design">
                                    <option value="arts" {{ old('category') == 'arts' ? 'selected' : '' }}>Arts, Design & Music</option>
                                    <option value="architecture" {{ old('category') == 'architecture' ? 'selected' : '' }}>Architecture & Urban Planning</option>
                                </optgroup>
                                <optgroup label="Literature & General">
                                    <option value="children" {{ old('category') == 'children' ? 'selected' : '' }}>Children's Books</option>
                                    <option value="fiction" {{ old('category') == 'fiction' ? 'selected' : '' }}>Fiction</option>
                                    <option value="non_fiction" {{ old('category') == 'non_fiction' ? 'selected' : '' }}>Non-Fiction</option>
                                    <option value="biographies" {{ old('category') == 'biographies' ? 'selected' : '' }}>Biographies & Memoirs</option>
                                    <option value="self_help" {{ old('category') == 'self_help' ? 'selected' : '' }}>Self-Help & Personal Development</option>
                                    <option value="leadership" {{ old('category') == 'leadership' ? 'selected' : '' }}>Leadership</option>
                                </optgroup>
                                <optgroup label="Academic & Research">
                                    <option value="research" {{ old('category') == 'research' ? 'selected' : '' }}>Research & Academic Publications</option>
                                    <option value="journals" {{ old('category') == 'journals' ? 'selected' : '' }}>Journals & Conference Proceedings</option>
                                    <option value="theses" {{ old('category') == 'theses' ? 'selected' : '' }}>Theses & Dissertations</option>
                                    <option value="government" {{ old('category') == 'government' ? 'selected' : '' }}>Government Publications</option>
                                    <option value="policies" {{ old('category') == 'policies' ? 'selected' : '' }}>Policies, Acts & Regulations</option>
                                    <option value="reports" {{ old('category') == 'reports' ? 'selected' : '' }}>Reports & White Papers</option>
                                </optgroup>
                                <optgroup label="Reference & More">
                                    <option value="reference" {{ old('category') == 'reference' ? 'selected' : '' }}>Reference Books</option>
                                    <option value="oer" {{ old('category') == 'oer' ? 'selected' : '' }}>Open Educational Resources</option>
                                    <option value="newspapers" {{ old('category') == 'newspapers' ? 'selected' : '' }}>Newspapers & Magazines</option>
                                    <option value="encyclopedias" {{ old('category') == 'encyclopedias' ? 'selected' : '' }}>Encyclopedias & Dictionaries</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Status</label>
                            @if($institution->type === 'bookstore')
                                <select name="status" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            @else
                                <select name="status" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            @endif
                            @error('status')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 4. SHELF LOCATION                          -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(37, 99, 235, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(37, 99, 235, 0.04); border-bottom: 1px solid rgba(37, 99, 235, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-map-pin" style="color: #2563eb;"></i> Shelf Location
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Shelf</label>
                            <select name="shelf_number" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="">Select Shelf</option>
                                @foreach($shelves ?? [] as $shelf)
                                    <option value="{{ $shelf->code }}" {{ old('shelf_number') == $shelf->code ? 'selected' : '' }}>
                                        {{ $shelf->code }} - {{ $shelf->name }} ({{ $shelf->current_count }}/{{ $shelf->capacity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('shelf_number')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Shelf Name</label>
                            <input type="text" name="shelf_name" value="{{ old('shelf_name') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., History Section">
                            @error('shelf_name')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Floor</label>
                            <input type="text" name="floor" value="{{ old('floor') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Ground, 1st, 2nd">
                            @error('floor')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Section</label>
                            <input type="text" name="section" value="{{ old('section') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="e.g., Fiction, Non-Fiction">
                            @error('section')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 5. FILES                                   -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(124, 58, 237, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(124, 58, 237, 0.04); border-bottom: 1px solid rgba(124, 58, 237, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-file" style="color: #7c3aed;"></i> Files
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Cover Image</label>
                            <input type="file" name="cover_image" accept="image/*"
                                   style="width: 100%; padding: 0.7rem; background: #faf8f5; border: 1px dashed #d6d2cb; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; cursor: pointer;">
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Max 2MB. Allowed: JPG, PNG, JPEG</p>
                            @error('cover_image')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if($institution->type !== 'bookstore')
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">PDF File</label>
                            <input type="file" name="file" accept=".pdf"
                                   style="width: 100%; padding: 0.7rem; background: #faf8f5; border: 1px dashed #d6d2cb; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; cursor: pointer;">
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Max 10MB. Allowed: PDF</p>
                            @error('file')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Total Pages</label>
                            <input type="number" name="total_pages" value="{{ old('total_pages') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="Enter total pages">
                            @error('total_pages')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 6. PRICING & STOCK (Bookstore)             -->
                <!-- ========================================== -->
                @if($institution->type === 'bookstore')
                <div style="background: white; border: 1px solid rgba(219, 87, 10, 0.15); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(219, 87, 10, 0.06); border-bottom: 1px solid rgba(219, 87, 10, 0.1);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-coin" style="color: #db570a;"></i> Pricing & Stock
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Price (TSh)</label>
                            <input type="number" name="price" step="0.01" value="{{ old('price') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                            @error('price')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0">
                            @error('stock_quantity')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 7. BOOKSTORE SETTINGS                      -->
                <!-- ========================================== -->
                <div style="background: white; border: 1px solid rgba(91, 33, 182, 0.12); border-radius: 0.75rem; overflow: hidden;">
                    <div style="padding: 0.6rem 1.25rem; background: rgba(91, 33, 182, 0.04); border-bottom: 1px solid rgba(91, 33, 182, 0.08);">
                        <span style="color: #1a1a2e; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="ti ti-shopping-cart" style="color: #5b21b6;"></i> Bookstore Settings
                        </span>
                    </div>
                    
                    <div style="padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Book Type</label>
                            <select name="book_type" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="softcopy" {{ old('book_type') == 'softcopy' ? 'selected' : '' }}>Softcopy Only</option>
                                <option value="hardcopy" {{ old('book_type') == 'hardcopy' ? 'selected' : '' }}>Hardcopy Only</option>
                                <option value="both" {{ old('book_type') == 'both' ? 'selected' : '' }}>Both (Softcopy + Hardcopy)</option>
                            </select>
                            @error('book_type')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Softcopy Price (TSh)</label>
                            <input type="number" name="softcopy_price" step="0.01" value="{{ old('softcopy_price') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                            @error('softcopy_price')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Hardcopy Price (TSh)</label>
                            <input type="number" name="hardcopy_price" step="0.01" value="{{ old('hardcopy_price') }}"
                                   style="width: 100%; padding: 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem;" placeholder="0.00">
                            @error('hardcopy_price')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1a1a2e; margin-bottom: 0.4rem;">Hardcopy Available</label>
                            <select name="hardcopy_available" style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="1" {{ old('hardcopy_available', 1) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('hardcopy_available', 1) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Is the hardcopy version available for purchase?</p>
                            @error('hardcopy_available')
                                <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- ========================================== -->
                <!-- 8. FORM ACTIONS                           -->
                <!-- ========================================== -->
                <div style="display: flex; gap: 1rem; padding-top: 0.5rem;">
                    <button type="submit" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 2rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-device-floppy"></i> 
                        @if($institution->type === 'bookstore')
                            Add to Inventory
                        @else
                            Save Book
                        @endif
                    </button>
                    <a href="{{ route('institution.books.index') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.95rem; transition: all 0.2s; cursor: pointer; text-decoration: none;">
                        Cancel
                    </a>
                </div>

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
        
        div[style*="padding: 1.25rem"] {
            padding: 1rem !important;
        }
        
        div[style*="display: flex; gap: 1rem; padding-top: 0.5rem;"] {
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